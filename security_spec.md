# Security Specification - Bluebird B2B Enterprise Hub

## Data Invariants
1. An **Order** must have a valid `clientId`, `vehicleId`, and `driverId`.
2. A **Vehicle** status can only be updated to 'Maintenance' if an `estimatedCompletionDate` is provided.
3. Only authenticated staff (simulated via `isSignedIn()`) can create or modify any data.
4. **Maintenance** status locks a vehicle from being assigned to new orders.

## The Dirty Dozen (Threat Payloads)

1. **Identity Spoofing**: Attempt to create a document with an ID containing malicious symbols like `../../secrets`.
2. **Shadow Field Injection**: Adding `isVerified: true` to a Client profile.
3. **Ghost Status**: Manually setting an Order to `Completed` without a payment being processed (Price = 0).
4. **Price Manipulation**: Updating an existing order's `price` to `0` after it's been active.
5. **Driver Hijacking**: Changing a Driver's status to `Available` while they are mid-trip in an `Active` order.
6. **Orphaned Order**: Creating an Order with a non-existent `vehicleId`.
7. **Negative Pricing**: Setting `price: -1000000` to drain revenue data.
8. **Maintenance Gap**: Setting a vehicle to `Maintenance` status but omitting the `estimatedCompletionDate`.
9. **Bulk Scrape**: Attempting to list all Clients without being signed in.
10. **Timeline Inversion**: Creating an order where `returnDate` is before `pickupDate`.
11. **PII Leak**: Unauthorized user attempting to `get` the full Driver list including private phone numbers.
12. **Status Shortcutting**: Moving an order from `Draft` directly to `Completed` skipping `Active`.

## Test Runner logic (firestore.rules)
- All writes must pass `isValidId()`.
- Updates must use `affectedKeys().hasOnly()` to prevent Shadow Field injection.
- Reference checks using `exists()` for relational integrity.
