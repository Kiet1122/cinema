@extends('customer.layouts.app')

@section('title', 'Đặt Vé - ' . $showtime->movie->Title)

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="container mx-auto px-4">
            <!-- Breadcrumb -->
            <nav class="mb-6">
                <ol class="flex items-center space-x-2 text-sm text-gray-500">
                    <li><a href="{{ route('customer.home') }}" class="hover:text-blue-600">Trang chủ</a></li>
                    <li><i class="fas fa-chevron-right text-xs"></i></li>
                    <li><a class="hover:text-blue-600">Phim</a></li>
                    <li><i class="fas fa-chevron-right text-xs"></i></li>
                    <li><a class="hover:text-blue-600">{{ Str::limit($showtime->movie->Title, 20) }}</a></li>
                    <li><i class="fas fa-chevron-right text-xs"></i></li>
                    <li class="text-gray-900 font-medium">Đặt vé</li>
                </ol>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Seat Selection -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">Chọn Ghế</h1>
                        <p class="text-gray-600 mb-6">{{ $showtime->movie->Title }} -
                            {{ \Carbon\Carbon::parse($showtime->StartTime)->format('H:i d/m/Y') }}
                        </p>

                        <!-- Screen -->
                        <div class="mb-8 text-center">
                            <div
                                class="bg-gradient-to-t from-gray-800 to-gray-600 text-white py-4 rounded-lg mx-auto max-w-md shadow-md">
                                <i class="fas fa-film mr-2"></i>Màn Hình
                            </div>
                        </div>

                        <!-- Seat Map -->
                        <div class="mb-8">
                            <div id="seatMap" class="flex flex-col items-center space-y-4">
                                @if($showtime->room->seats->count() > 0)
                                    <!-- Hiển thị ghế từ server nếu JavaScript không hoạt động -->
                                    <div class="text-center text-gray-500">
                                        Đang tải sơ đồ ghế...
                                    </div>
                                @else
                                    <div class="text-center text-red-500 p-8">
                                        <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                                        <p>Không có dữ liệu ghế cho phòng này</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Seat Legend -->
                        <div class="flex flex-wrap justify-center gap-4 text-sm mb-6">
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 bg-green-200 border border-green-400 rounded"></div>
                                <span class="text-gray-600">Standard</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 bg-yellow-200 border border-yellow-400 rounded"></div>
                                <span class="text-gray-600">VIP</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 bg-purple-200 border border-purple-400 rounded"></div>
                                <span class="text-gray-600">Couple</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 bg-blue-200 border border-blue-400 rounded"></div>
                                <span class="text-gray-600">Đang chọn</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-4 h-4 bg-gray-300 border border-gray-400 rounded"></div>
                                <span class="text-gray-600">Đã đặt</span>
                            </div>
                        </div>

                        <!-- Price Info -->
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                            <h3 class="font-semibold text-blue-900 mb-2">Thông Tin Giá</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <!-- Giá vé phim -->
                                <div class="space-y-2">
                                    <h4 class="font-semibold text-blue-800">Giá Vé Phim</h4>
                                    <div class="flex justify-between">
                                        <span class="text-blue-700">Vé {{ $showtime->room->RoomType }}:</span>
                                        <span class="font-semibold">{{ number_format($showtime->Price) }}₫</span>
                                    </div>
                                </div>

                                <!-- Giá ghế -->
                                <div class="space-y-2">
                                    <h4 class="font-semibold text-blue-800">Giá Ghế</h4>
                                    @php
                                        $seatTypes = $showtime->room->seats->groupBy('SeatType');
                                        $orderedTypes = ['Standard', 'VIP', 'Couple'];
                                    @endphp
                                    @foreach($orderedTypes as $type)
                                        @if(isset($seatTypes[$type]))
                                            <div class="flex justify-between">
                                                <span class="text-blue-700">{{ $type }}:</span>
                                                <span
                                                    class="font-semibold">{{ number_format($seatTypes[$type]->first()->Price) }}₫</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Booking Summary -->
                <div class="space-y-6">
                    <!-- Order Summary -->
                    <div class="bg-white rounded-2xl shadow-sm p-6 sticky top-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Thông Tin Đặt Vé</h2>

                        <!-- Movie Info -->
                        <div class="space-y-3 mb-6">
                            <div class="flex items-start space-x-3">
                                <img src="{{ asset('storage/movies/' . $showtime->movie->PosterURL) }}"
                                    alt="{{ $showtime->movie->Title }}" class="w-16 h-20 object-cover rounded-lg shadow">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900">{{ $showtime->movie->Title }}</h3>
                                    <p class="text-sm text-gray-600">{{ $showtime->room->RoomType }}</p>
                                    <p class="text-sm text-gray-600">
                                        {{ \Carbon\Carbon::parse($showtime->StartTime)->format('H:i d/m/Y') }}
                                    </p>
                                    <p class="text-sm text-gray-600">{{ $showtime->room->theater->Name }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Selected Seats -->
                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-900 mb-3">Ghế Đã Chọn</h3>
                            <div id="selectedSeats" class="space-y-2 max-h-40 overflow-y-auto">
                                <p class="text-gray-500 text-sm">Chưa chọn ghế</p>
                            </div>
                        </div>

                        <!-- Voucher -->
                        <div class="mb-6">
                            <h3 class="font-semibold text-gray-900 mb-3">Mã Giảm Giá</h3>
                            <div class="flex space-x-2">
                                <input type="text" id="voucherCode" placeholder="Nhập mã giảm giá"
                                    class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
                                <button id="applyVoucher"
                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                                    Áp dụng
                                </button>
                            </div>
                            <div id="voucherMessage" class="mt-2 text-sm"></div>
                        </div>

                        <!-- Price Summary -->
                        <!-- Price Summary -->
                        <div class="space-y-3 border-t border-gray-200 pt-4">
                            <!-- FIXED: Show ticket price with quantity -->
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Giá vé phim (<span id="ticketQuantity">0</span> vé):</span>
                                <span id="ticketPrice">0₫</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tổng tiền ghế:</span>
                                <span id="seatsSubtotal">0₫</span>
                            </div>
                            <div class="flex justify-between text-sm border-b border-gray-200 pb-2">
                                <span class="text-gray-600">Tạm tính:</span>
                                <span id="subtotalAmount">0₫</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Giảm giá:</span>
                                <span id="discountAmount" class="text-green-600">0₫</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold border-t border-gray-200 pt-3">
                                <span class="text-gray-900">Tổng cộng:</span>
                                <span id="totalAmount" class="text-blue-600">0₫</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <form id="bookingForm" action="{{ route('customer.booking.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="showtime_id" value="{{ $showtime->ShowtimeID }}">
                            <input type="hidden" name="seats" id="selectedSeatsInput">
                            <input type="hidden" name="voucher_id" id="voucherId">
                            <input type="hidden" name="payment_method" value="cash" id="paymentMethod">

                            <button type="submit" id="submitButton"
                                class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold mt-6 hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed"
                                disabled>
                                Tiếp Tục Thanh Toán
                            </button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const showtimeId = {{ $showtime->ShowtimeID }};
            const room = @json($showtime->room);
            const bookedSeats = @json($bookedSeats);
            const availableVouchers = @json($availableVouchers ?? []);
            const ticketPrice = parseFloat({{ $showtime->Price }}); // Lấy giá vé phim từ showtime

            let selectedSeats = [];
            let appliedVoucher = null;

            console.log('=== DEBUG SEAT DATA ===');
            console.log('Room:', room);
            console.log('Seats:', room.seats);
            console.log('Booked seats:', bookedSeats);
            console.log('Available vouchers:', availableVouchers);
            console.log('Ticket price:', ticketPrice);

            // Format currency without decimal places
            function formatCurrency(amount) {
                if (isNaN(amount) || amount === null || amount === undefined) {
                    return '0₫';
                }
                return new Intl.NumberFormat('vi-VN').format(Math.round(amount)) + '₫';
            }

            // Get seat price from actual data with proper validation
            function getSeatPrice(seat) {
                if (!seat) {
                    console.error('Seat is null or undefined');
                    return 0;
                }
                if (!seat.Price && seat.Price !== 0) {
                    console.error('Seat price not found for seat:', seat);
                    return 0;
                }
                const price = parseFloat(seat.Price);
                if (isNaN(price)) {
                    console.error('Seat price is not a number:', seat.Price, 'for seat:', seat);
                    return 0;
                }
                return price;
            }

            // Initialize seat map with ALL seats from database but 20 per row
            function initializeSeatMap() {
                const seatMap = document.getElementById('seatMap');
                const seats = room.seats || [];

                seatMap.innerHTML = ''; // Clear loading message

                if (seats.length === 0) {
                    seatMap.innerHTML = `
                                                <div class="text-center text-red-500 p-8">
                                                    <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                                                    <p>Không có dữ liệu ghế cho phòng này</p>
                                                </div>
                                            `;
                    return;
                }

                // Group ALL seats by row from database
                const seatsByRow = {};
                seats.forEach(seat => {
                    if (!seat.SeatNumber) {
                        console.warn('Seat missing SeatNumber:', seat);
                        return;
                    }

                    // Extract row letter (A, B, C, etc.)
                    const row = seat.SeatNumber.replace(/[0-9]/g, '');
                    if (!seatsByRow[row]) {
                        seatsByRow[row] = [];
                    }
                    seatsByRow[row].push(seat);
                });

                console.log('All seats by row:', seatsByRow);

                // Create rows with ALL seats but maximum 20 per row
                Object.keys(seatsByRow).sort().forEach(row => {
                    // Sort seats by number in this row
                    seatsByRow[row].sort((a, b) => {
                        const aNum = parseInt(a.SeatNumber.replace(row, '')) || 0;
                        const bNum = parseInt(b.SeatNumber.replace(row, '')) || 0;
                        return aNum - bNum;
                    });

                    // Split seats into chunks of 20 for each row
                    const seatChunks = [];
                    for (let i = 0; i < seatsByRow[row].length; i += 20) {
                        seatChunks.push(seatsByRow[row].slice(i, i + 20));
                    }

                    // Create a row for each chunk
                    seatChunks.forEach((chunk, chunkIndex) => {
                        const rowDiv = document.createElement('div');
                        rowDiv.className = 'flex items-center space-x-2 mb-3';

                        // Row label (add suffix for additional rows if needed)
                        const rowLabel = document.createElement('div');
                        rowLabel.className = 'w-8 text-center font-semibold text-gray-700';
                        rowLabel.textContent = chunkIndex === 0 ? row : `${row}${chunkIndex + 1}`;
                        rowDiv.appendChild(rowLabel);

                        // Create exactly 20 seats for this row chunk
                        for (let seatNum = 1; seatNum <= 20; seatNum++) {
                            const actualSeatIndex = (chunkIndex * 20) + (seatNum - 1);
                            const seat = seatsByRow[row][actualSeatIndex];

                            if (seat) {
                                // Seat exists, create normal button
                                const seatButton = createSeatButton(seat);
                                rowDiv.appendChild(seatButton);
                            } else {
                                // No seat at this position, create disabled placeholder
                                const placeholderButton = document.createElement('button');
                                placeholderButton.type = 'button';
                                placeholderButton.className = 'w-8 h-8 rounded text-xs font-medium bg-gray-200 border border-gray-300 cursor-not-allowed flex items-center justify-center opacity-50';
                                placeholderButton.innerHTML = `<span class="font-bold text-gray-500">${seatNum}</span>`;
                                placeholderButton.title = `Vị trí trống`;
                                placeholderButton.disabled = true;
                                rowDiv.appendChild(placeholderButton);
                            }
                        }

                        seatMap.appendChild(rowDiv);
                    });
                });

                console.log('Total seats displayed:', document.querySelectorAll('#seatMap button:not([disabled])').length);
                console.log('Total placeholders:', document.querySelectorAll('#seatMap button[disabled]').length);
            }

            // Create seat button element using actual data
            function createSeatButton(seat) {
                const seatButton = document.createElement('button');
                seatButton.type = 'button';

                const isBooked = bookedSeats.includes(seat.SeatID);
                const isSelected = selectedSeats.some(s => s.SeatID === seat.SeatID);
                const seatPrice = getSeatPrice(seat);

                // Determine seat color based on type
                let seatClass = 'w-8 h-8 rounded text-xs font-medium transition-all duration-200 border flex items-center justify-center ';

                if (isBooked) {
                    seatClass += 'bg-gray-300 border-gray-400 cursor-not-allowed';
                } else if (isSelected) {
                    seatClass += 'bg-blue-200 border-blue-400';
                } else {
                    // Different colors for different seat types
                    switch (seat.SeatType) {
                        case 'VIP':
                            seatClass += 'bg-yellow-200 border-yellow-400 hover:bg-yellow-300';
                            break;
                        case 'Couple':
                            seatClass += 'bg-purple-200 border-purple-400 hover:bg-purple-300';
                            break;
                        default: // Standard
                            seatClass += 'bg-green-200 border-green-400 hover:bg-green-300';
                    }
                }

                seatButton.className = seatClass;
                const seatNumber = seat.SeatNumber.replace(/[A-Z]/g, '');
                seatButton.innerHTML = `<span class="font-bold">${seatNumber}</span>`;
                seatButton.title = `${seat.SeatNumber} - ${seat.SeatType} - ${formatCurrency(seatPrice)}`;

                // Store seat data for easy retrieval
                seatButton.dataset.seatId = seat.SeatID;
                seatButton.dataset.seatNumber = seat.SeatNumber;

                if (!isBooked) {
                    seatButton.addEventListener('click', function () {
                        const clickedSeat = room.seats.find(s => s.SeatID == this.dataset.seatId);
                        if (clickedSeat) {
                            toggleSeat(clickedSeat);
                        }
                    });
                }

                return seatButton;
            }

            // Toggle seat selection
            function toggleSeat(seat) {
                console.log('Toggling seat:', seat.SeatNumber, 'ID:', seat.SeatID);
                console.log('Seat price:', getSeatPrice(seat));

                const index = selectedSeats.findIndex(s => s.SeatID === seat.SeatID);

                if (index > -1) {
                    // Deselect seat
                    selectedSeats.splice(index, 1);
                    console.log('Seat deselected:', seat.SeatNumber);
                } else {
                    // Select seat
                    selectedSeats.push(seat);
                    console.log('Seat selected:', seat.SeatNumber);
                }

                updateSelectedSeatsDisplay();
                updatePriceSummary();
                updateSubmitButton();
                updateSeatMap();
            }

            // Update seat map visual
            function updateSeatMap() {
                const seatButtons = document.querySelectorAll('#seatMap button:not([disabled])');
                console.log('Updating seat map for', seatButtons.length, 'seats');

                seatButtons.forEach(button => {
                    const seatId = button.dataset.seatId;
                    const seat = room.seats.find(s => s.SeatID == seatId);

                    if (seat) {
                        const isSelected = selectedSeats.some(s => s.SeatID == seatId);
                        const isBooked = bookedSeats.includes(parseInt(seatId));
                        const seatType = seat.SeatType || 'Standard';
                        const seatPrice = getSeatPrice(seat);

                        let seatClass = 'w-8 h-8 rounded text-xs font-medium transition-all duration-200 border flex items-center justify-center ';

                        if (isBooked) {
                            seatClass += 'bg-gray-300 border-gray-400 cursor-not-allowed';
                        } else if (isSelected) {
                            seatClass += 'bg-blue-200 border-blue-400';
                        } else {
                            // Different colors for different seat types
                            switch (seatType) {
                                case 'VIP':
                                    seatClass += 'bg-yellow-200 border-yellow-400 hover:bg-yellow-300';
                                    break;
                                case 'Couple':
                                    seatClass += 'bg-purple-200 border-purple-400 hover:bg-purple-300';
                                    break;
                                default: // Standard
                                    seatClass += 'bg-green-200 border-green-400 hover:bg-green-300';
                            }
                        }

                        button.className = seatClass;
                        button.title = `${seat.SeatNumber} - ${seatType} - ${formatCurrency(seatPrice)}`;
                    }
                });
            }

            // Update selected seats display
            function updateSelectedSeatsDisplay() {
                const selectedSeatsDiv = document.getElementById('selectedSeats');
                const selectedSeatsInput = document.getElementById('selectedSeatsInput');

                if (selectedSeats.length === 0) {
                    selectedSeatsDiv.innerHTML = '<p class="text-gray-500 text-sm">Chưa chọn ghế</p>';
                    selectedSeatsInput.value = '';
                } else {
                    selectedSeatsDiv.innerHTML = selectedSeats.map(seat => {
                        const seatPrice = getSeatPrice(seat);
                        console.log(`Displaying seat ${seat.SeatNumber} with price:`, seatPrice);
                        return `
                                                    <div class="flex justify-between items-center text-sm p-2 bg-gray-50 rounded">
                                                        <div>
                                                            <span class="font-semibold text-gray-700">${seat.SeatNumber}</span>
                                                            <span class="text-gray-500 text-xs ml-2">(${seat.SeatType})</span>
                                                        </div>
                                                        <span class="font-semibold">${formatCurrency(seatPrice)}</span>
                                                    </div>
                                                `;
                    }).join('');
                    selectedSeatsInput.value = JSON.stringify(selectedSeats.map(s => s.SeatID));
                }

                console.log('Selected seats updated:', selectedSeats.length);
            }

            // Update price summary using actual seat prices + ticket price
            // Update price summary using actual seat prices + ticket price
            // Update price summary using actual seat prices + ticket price
            function updatePriceSummary() {
                let seatsSubtotal = 0;

                // Calculate seats subtotal with proper validation
                selectedSeats.forEach(seat => {
                    const seatPrice = getSeatPrice(seat);
                    console.log(`Adding seat ${seat.SeatNumber} price:`, seatPrice);
                    seatsSubtotal += seatPrice;
                });

                console.log('Calculated seats subtotal:', seatsSubtotal);

                // FIXED: Calculate total (ticket price * number of seats + seats subtotal)
                const numberOfTickets = selectedSeats.length;
                const totalTicketPrice = ticketPrice * numberOfTickets; // Giá vé phim cơ bản
                let subtotal = totalTicketPrice + seatsSubtotal; // Tổng = (giá vé * số lượng) + phí ghế

                let discount = 0;

                if (appliedVoucher) {
                    if (appliedVoucher.voucher.DiscountType === 'Percent') {
                        discount = (subtotal * appliedVoucher.voucher.Value) / 100;
                    } else {
                        discount = appliedVoucher.voucher.Value;
                    }
                    discount = Math.min(discount, subtotal);
                }

                const total = subtotal - discount;

                console.log('Final prices - Tickets:', numberOfTickets, 'Ticket Price:', ticketPrice, 'Total Ticket:', totalTicketPrice, 'Seats:', seatsSubtotal, 'Subtotal:', subtotal, 'Discount:', discount, 'Total:', total);

                // FIXED: Update all display elements
                document.getElementById('ticketQuantity').textContent = numberOfTickets;
                document.getElementById('ticketPrice').textContent = formatCurrency(totalTicketPrice);
                document.getElementById('seatsSubtotal').textContent = formatCurrency(seatsSubtotal);
                document.getElementById('subtotalAmount').textContent = formatCurrency(subtotal);
                document.getElementById('discountAmount').textContent = formatCurrency(discount);
                document.getElementById('totalAmount').textContent = formatCurrency(total);
            }
            // Update submit button state
            function updateSubmitButton() {
                const submitButton = document.getElementById('submitButton');
                submitButton.disabled = selectedSeats.length === 0;
                console.log('Submit button updated - disabled:', submitButton.disabled);
            }

            // Apply voucher
            document.getElementById('applyVoucher').addEventListener('click', function () {
                const voucherCode = document.getElementById('voucherCode').value.trim();
                const voucherMessage = document.getElementById('voucherMessage');

                if (!voucherCode) {
                    voucherMessage.innerHTML = '<span class="text-red-600">Vui lòng nhập mã giảm giá</span>';
                    return;
                }

                // Find voucher in available vouchers
                const voucher = availableVouchers.find(v => v.voucher && v.voucher.Code === voucherCode);

                if (!voucher) {
                    voucherMessage.innerHTML = '<span class="text-red-600">Mã giảm giá không hợp lệ</span>';
                    appliedVoucher = null;
                    document.getElementById('voucherId').value = '';
                } else {
                    appliedVoucher = voucher;
                    document.getElementById('voucherId').value = voucher.voucher.VoucherID;
                    voucherMessage.innerHTML = `<span class="text-green-600">Áp dụng mã ${voucherCode} thành công!</span>`;
                }

                updatePriceSummary();
            });

            // Initialize
            initializeSeatMap();
        });
    </script>

    <style>
        #seatMap {
            max-width: 100%;
            overflow-x: auto;
        }

        #seatMap button {
            min-width: 32px;
            min-height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #seatMap button:not(:disabled):hover {
            transform: scale(1.05);
        }

        #seatMap .flex {
            flex-wrap: nowrap;
        }

        #selectedSeats::-webkit-scrollbar {
            width: 4px;
        }

        #selectedSeats::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        #selectedSeats::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        #selectedSeats::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
@endsection