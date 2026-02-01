# Payment Integration & Invoicing Implementation Plan

## Goal Description

Integrate HyperPay payment flow securely into the flight booking process and implement automated invoice generation. This ensures tickets are only issued after successful payment and provides customers with tax invoices.

## User Review Required

> [!IMPORTANT]
> This change introduces a strict dependency on payment for ticket issuance. Ensure `HyperPay` credentials in `.env` are valid for the environment (Test/Live).

## Proposed Changes

### 1. Dependencies

#### [NEW] `composer.json`

- Require `mpdf/mpdf` for invoice generation (Better Arabic support).

### 2. Services

#### [NEW] `app/Services/InvoiceService.php`

- Create a service to handle PDF generation using mPDF.
- Configure fonts for Arabic support.

### 2. Database

#### [MODIFY] `bookings` Table

- Ensure `status` and `ticket_status` columns can handle the flow:
    - `status`: `pending` (initial) -> `paid` (after payment) -> `confirmed` (after ticketing).
    - Or separate `payment_status` column?
    - **Decision:** Keep it simple. Use `status` for overall state and `ticket_status` for Travelopro state.
    - `status`: `pending` -> `paid` -> `completed` (or `issued`).
    - `ticket_status`: `Booked` -> `Ticketed`.

### 3. Models

#### [MODIFY] `app/Models/Booking.php`

- Add necessary scopes or methods if needed (e.g., `scopePaid`, `scopePending`).

### 4. Controllers

#### [MODIFY] `app/Http/Controllers/Api/FlightController.php`

- `book`: Remain as is (creates PNR). Returns `UniqueID` and `TotalAmount`.
- `initiatePayment`: (New/Modified) Link to `PaymentController` logic but context-aware for Flights. Or just use `PaymentController` directly?
    - **Approach:** Use `PaymentController` directly but ensure the `order_id` passed is the `booking_id` (or `booking_reference`).
- `orderTicket`:
    - **CRITICAL:** Add check: `if ($booking->status !== 'paid') return error;`
    - Update to generate Invoice PDF after success.

#### [MODIFY] `app/Http/Controllers/Api/PaymentController.php`

- `verify`:
    - Find `Booking` by `booking_reference` (which is passed as `trackId` or `merchantTransactionId`).
    - Update `Booking` status to `paid`.

### 5. Views

#### [NEW] `resources/views/invoices/flight_invoice.blade.php`

- Design a professional invoice layout.

## Verification Plan

### Automated Tests

- **Unit Test:** Mock `HyperPayService` response and test `FlightController::orderTicket` blocks unpaid bookings.
- **Manual Verification:**
    1.  **Book:** Create a booking -> DB status `pending`.
    2.  **Pay:** Call `payment/initiate` -> `payment/verify`. Check DB status updates to `paid`.
    3.  **Issue:** Call `orderTicket`. Should succeed.
    4.  **Invoice:** Check if PDF is generated/returned.
    5.  **Negative Test:** Call `orderTicket` on a `pending` booking. Should fail.
