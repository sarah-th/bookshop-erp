# Book Distribution & Sales Management System

A **Laravel + Filament** admin panel application designed for a **book supplier/distributor** business. It covers inventory management, client tracking, and a full sales quotation workflow.

---

## Core Modules

### 1. Books Management
Manages the book catalog with detailed attributes:
- **Name & ISBN** – Book identification
- **Category** – Subjects like Math, Arabic, English, Science, Physics, Chemistry, Biology, History, Art, and more
- **Level** – Grade levels from Kindergarten/Play all the way up to Grade 12, Primary, and Prep (covering the full school curriculum)
- **Supplier & Publisher** – Tracks where each book comes from
- **Stock** – Current quantity, last purchase/sell dates
- **Cost & Currency** – Pricing with multi-currency support

### 2. Clients Management
Tracks customers with:
- **Type** – Either a **School** or a **Company**
- **Contact info** – Phone, email, address
- **Payment method** – How clients pay

### 3. Suppliers Management
Manages the vendors/suppliers that provide books.

### 4. Publishers Management
Tracks book publishers.

### 5. Currencies Management
Supports multiple currencies for book pricing.

### 6. Quotations (Sales Module)
The heart of the sales workflow:
- Creates numbered **sales quotations** for clients
- Adds **line items** (books with quantity, unit price, and per-item discount)
- Calculates **net values**, **subtotals**, and applies a **general discount**
- Tracks quotation **status**: `Draft → Sent → Approved / Rejected`
- Sets a **valid until** date for the offer

---

## Summary

This system helps a **book distributor** manage their inventory (books from various suppliers/publishers), their client base (schools and companies), and their **sales pipeline** through quotations — all via a clean admin dashboard built with Filament.
