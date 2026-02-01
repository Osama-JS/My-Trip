# Admin Panel Implementation Plan

## Goal Description

Build a professional Admin Dashboard to track Bookings (Current, Past, Cancelled), Users (Details, History), and Logs (Search, API).

## Proposed Changes

### 1. Booking Management

#### [MODIFY] `app/Http/Controllers/Admin/BookingController.php`

- Add `index()`: Return view for booking list.
- Add `getData()`: Return JSON for DataTables (booking source: DB).
- Add `show($id)`: View booking details (Passengers, Invoice, Logs).
- Add `cancel($id)`: Admin cancellation action.

#### [NEW] `resources/views/admin/bookings/index.blade.php`

- DataTables listing: ID, Ref, User, Route (From-To), Date, Status (Badge), Amount, Actions (View, Invoice).

#### [NEW] `resources/views/admin/bookings/show.blade.php`

- Detailed view: Booking Info, Passenger List, Price Breakdown, Linked API Logs.

### 2. User Statistics

#### [MODIFY] `app/Http/Controllers/Admin/UserController.php`

- Add `activity($id)`: Return view with tabs (Bookings, Searches).

#### [NEW] `resources/views/admin/users/activity.blade.php`

- Tabs:
    - **Overview:** Stats (Total Spent, Total Bookings).
    - **Bookings:** List of this user's bookings.
    - **Searches:** List of `flight_search_logs` for this user.

### 3. Logs & Monitoring

#### [NEW] `app/Http/Controllers/Admin/ReportController.php`

- `searchLogs()`: View user search history.
- `apiLogs()`: View Travelopro API calls (Success/Errors).

#### [NEW] `resources/views/admin/reports/search_logs.blade.php`

#### [NEW] `resources/views/admin/reports/api_logs.blade.php`

### 4. Routes

#### [MODIFY] `routes/web.php`

- Register new routes for Bookings (DB), User Activity, and Reports.
