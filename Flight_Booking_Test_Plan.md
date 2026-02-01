# Flight Booking Lifecycle Test Plan

## Goal Description

Create a comprehensive automated test suite (`tests/Feature/FlightBookingFlowTest.php`) to verify the entire flight booking lifecycle, from search to cancellation.

## User Review Required

> [!NOTE]
> This test suite relies on **Mocking** external services (Travelopro & HyperPay). It does NOT make real API calls or execute real payments.

## Proposed Changes

### 1. New Test File

#### [NEW] `tests/Feature/FlightBookingFlowTest.php`

- **`test_full_booking_lifecycle`**:
    1.  **Search**: Simulate flight search.
    2.  **Book**: Simulate booking (PNR creation). Verify DB persistence (`pending`).
    3.  **Pay**: Simulate successful payment. Verify DB update (`paid`).
    4.  **Issue**: Simulate ticket issuance. Verify DB update (`confirmed`, `ticketed`) and Invoice generation.
- **`test_cancellation_flow`**:
    1.  **Setup**: Create a confirmed booking in DB.
    2.  **Cancel**: Simulate cancellation request. Verify DB status update (`cancelled`).
- **`test_ticket_issuance_fails_without_paymابدent`**:
    1.  **Setup**: Create a `pending` booking.
    2.  **Issue**: Attempt to issue ticket. Verify failure (402 Payment Required).

### 2. Mocking Strategy

- **TraveloproService**: Mock responses for `searchFlights`, `createBooking`, `orderTicket`, `cancelBooking`.
- **HyperPayService**: Mock `getPaymentStatus` and `isSuccessful` to simulate payment success/failure.

## Verification Plan

### Automated Tests

- Run `php artisan test tests/Feature/FlightBookingFlowTest.php` to execute the suite.
