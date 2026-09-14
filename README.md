# CKM Talent Pipeline & Jobs Tracker for Perfex CRM

Specialized Voice Over, Actor & Creative Performer Job, Quote & Audition Pipeline module for Perfex CRM.

## Features
- **Native CKM Modules Integration:** Nests cleanly under the existing `CKM Modules` parent menu.
- **Audition & Job Pipeline:** Drag-and-drop Kanban board across 7 workflow stages (Quote/Audition Sent, Shortlisted/On Hold, Won/Booked, In Production, Delivered, Completed, Lost).
- **Voice Over / Actor Specifics:** Basic Session Fee (BSF) vs. Usage/Buyout fee breakdown, media types, territory licensing, session links (Source-Connect, Cleanfeed, Zoom), and audio delivery specs.
- **Agent / Platform Commission Calculator:** Auto-calculates Gross vs. Net take-home earnings based on agent cut (10%–20%).
- **1-Click Perfex Invoicing:** Converts Won / Completed jobs directly into native Perfex CRM Invoices with line items for BSF and Licensing Rights.
- **Analytics & Win/Loss Reporting:** Track Audition-to-Booking conversion rate, Agent & platform scorecard, and loss reasons post-mortem.
- **Automated Buyout Expiration Radar:** Perfex cron hook alerts you 30 days prior to license expiry for passive renewal revenue.

## Installation
1. Copy the `ckm_talent_pipeline` folder into your Perfex CRM `modules/` directory:
   ```bash
   cp -r ckm_talent_pipeline /path-to-perfex/modules/
   ```
2. Log into your Perfex CRM Admin dashboard.
3. Navigate to **Setup -> Modules**.
4. Find **CKM Talent Pipeline & Jobs Tracker** and click **Activate**.
5. Access the module under **CKM Modules -> Jobs & Audition Pipeline**.
