# Barcode & Serial Number Tracking System — Technical & Operational Documentation

This document explains the architecture, business logic, database relationships, and step-by-step operational workflows of the **Barcode and Serial Number Tracking System** in **itech inventory**.

---

## 1. Core Concepts & Item Classification

In this system, products are classified into two tracking types:

| Feature | 📦 Non-Serialized Product | 🔢 Serialized Product |
|---|---|---|
| **Tracking Level** | Bulk stock quantity (e.g. 50 cables, 100 adapters) | Individual physical unit tracking (e.g. Laptops, GPUs, Phones, Motherboards) |
| **Product Barcode (`products.barcode`)** | Vendor/Manufacturer SKU barcode on the packaging | Vendor/Manufacturer SKU barcode on the model packaging |
| **Unit Serial Barcode (`product_serials.serial_number`)** | Not required | **Mandatory & Unique** per physical unit box |
| **Sales Verification** | Verifies total quantity in stock > 0 | Verifies the **exact scanned serial is in stock & `available`** |
| **Warranty & Returns** | Tracked by invoice date | Tracked by **exact serial number lookup** |

---

## 2. End-to-End Operational Lifecycle

```
+-----------------------------------------------------------------------------------+
| 1. ADD PRODUCT                                                                    |
|    Define product name, model, warranty days, serialized toggle, and scan vendor   |
|    barcode (or click Auto-Generate).                                              |
+-----------------------------------------+-----------------------------------------+
                                          |
                                          v
+-----------------------------------------------------------------------------------+
| 2. PURCHASE (VENDOR STOCK-IN)                                                     |
|    - Scan vendor product barcode to auto-select item.                             |
|    - Scan box serial barcodes one-by-one.                                         |
|    - Quantity auto-increments with each scan.                                     |
|    - Serials saved into `product_serials` with status = 'available'.              |
+-----------------------------------------+-----------------------------------------+
                                          |
                                          v
+-----------------------------------------------------------------------------------+
| 3. SALES POS CHECKOUT (SELL & VERIFY)                                             |
|    - Scan the unit serial barcode (or product barcode).                           |
|    - Real-time verification: checks item is in stock & 'available'.               |
|    - Prevents duplicate scans or selling already-sold units.                      |
|    - On checkout: serial status changes to 'sold' linked to `sales_item_id`.      |
+-----------------------------------------+-----------------------------------------+
                                          |
                     +--------------------+--------------------+
                     |                                         |
                     v                                         v
+---------------------------------------+   +---------------------------------------+
| 4. WARRANTY CLAIM VERIFICATION        |   | 5. PRODUCT RETURN & RESTOCKING        |
|    - Scan returned unit serial.       |   |    - Scan serial to find sale order.  |
|    - Instantly pulls sale invoice,    |   |    - On return completion:            |
|      customer name, sale date.        |   |      serial status transitions from   |
|    - Calculates active days remaining |   |      'sold' back to 'available'.      |
|      (Valid vs. Expired).             |   |    - Restocked into inventory.        |
+---------------------------------------+   +---------------------------------------+
```

---

## 3. Step-by-Step Workflow Guide

### Step 1: Adding a Product to Catalog (`/products`)
1. Go to **Product Catalog** ➔ Click **Add New Product**.
2. Fill in Brand, Category, Product Name, and Model.
3. **Vendor Barcode / SKU**:
   - Scan the manufacturer barcode on the box with your scanner gun.
   - *Optional*: If the item has no barcode, click **Auto-Generate** (generates a unique `ITP-XXXXXX` code).
4. **Serialized Product Toggle**:
   - Enable `[x] Serialized Product` if tracking unique individual serial numbers.
   - Leave disabled for bulk items (cables, thermal paste, etc.).
5. Set Warranty (in days).
6. Click **Save Product**.

---

### Step 2: Receiving Stock from Vendor (`/purchase`)
1. Go to **Purchase List** ➔ Click **Add Purchase**.
2. **Scan Vendor Barcode**: Place cursor in the top scanner box and scan the box barcode. The system instantly auto-selects the product in the dropdown.
3. **For Serialized Products**:
   - The **"Scan Received Unit Serials"** panel opens.
   - Scan each unit's serial barcode with your scanner gun.
   - Each scan adds a visual serial badge and **automatically increments the Purchase Quantity**.
4. Enter Vendor and Unit Cost Price.
5. Click **Submit Purchase**.
   - Stock is incremented in `inventories`.
   - Each serial is saved in `product_serials` with `status = 'available'`.

---

### Step 3: Selling & POS Checkout Verification (`/sales/create`)
1. Open **Sales ➔ Add Sale Order**.
2. **Scan Barcode / Serial Scanner Box**:
   - **Scanning a Unit Serial Barcode**:
     - The system validates that the serial exists and is currently **`available`**.
     - If the serial is already sold or in the cart, it blocks it with an error alert and audio beep.
     - If valid, it adds the product to the cart with the verified serial number tag attached.
   - **Scanning a Product Barcode (Non-Serialized)**:
     - Checks available stock and adds/increments quantity in the cart.
3. Enter Customer details and Payment.
4. Click **Create Sale**.
   - Stock is deducted from `inventories`.
   - The scanned serials in `product_serials` are updated to `status = 'sold'` with their `sales_item_id` attached.

---

### Step 4: Warranty Claim Lookup (`/warranties/create`)
1. When a customer brings an item for warranty inspection, open **Warranties ➔ Register Warranty Claim**.
2. Scan the unit's **Serial Barcode** in the search field.
3. The system immediately retrieves:
   - Original Sale Invoice number & Customer details.
   - Sale Date & Warranty Expiration Date.
   - Live badge: **`X Days Remaining`** (Active) or **`Expired X days ago`**.
4. If valid, click **Claim Warranty** to register the RMA inspection log.

---

### Step 5: Product Return & Restocking (`/returns/create`)
1. Open **Product Returns ➔ Create Product Return**.
2. Scan the returned unit's **Serial Barcode**.
3. The system locates the exact customer sale invoice and auto-loads the sale line items.
4. Specify return quantity, condition (**Good** vs **Damaged**), and reason.
5. When the store manager approves and completes the return:
   - If condition is **Good**: The serial is restocked and its status returns to **`available`** in `product_serials`.
   - If condition is **Damaged**: The serial status changes to **`damaged`**.
   - Inventory stock count is restored.

---

## 4. Database Schema & Architecture

### `products` Table
| Column | Type | Description |
|---|---|---|
| `id` | BigInt (PK) | Unique product identifier |
| `name` | String | Product name |
| `model` | String | Model number |
| `barcode` | String (Unique, Nullable) | Vendor/Manufacturer SKU barcode |
| `is_serialized` | Boolean | `1` = Serialized tracking, `0` = Bulk inventory |
| `warranty` | Integer | Warranty validity in days |
| `status` | Enum (0, 1) | Active / Inactive |

### `product_serials` Table
| Column | Type | Description |
|---|---|---|
| `id` | BigInt (PK) | Unique serial record identifier |
| `product_id` | BigInt (FK) | Reference to `products.id` |
| `purchase_id` | BigInt (FK, Nullable) | Procurement purchase batch |
| `sales_item_id` | BigInt (FK, Nullable) | Linked to sold line item (`sales_items.id`) |
| `serial_number` | String (Unique) | Physical serial barcode value |
| `status` | Enum | `available`, `sold`, `damaged`, `returned` |

---

## 5. Universal Barcode Lookup API

The application exposes a unified lookup endpoint used by all scanner fields across the app:

**Endpoint**: `GET /products/barcode-lookup?code={SCANNED_VALUE}`

**Response format (Serial Match)**:
```json
{
  "success": true,
  "type": "serial",
  "status": "available",
  "serial_number": "SN-9823411",
  "product": {
    "id": 12,
    "name": "ASUS ROG Strix RTX 4070 Ti",
    "model": "ROG-STRIX-RTX4070TI",
    "barcode": "4711081982341",
    "stock": 5,
    "warranty_days": 1095,
    "selling_price": 98500.00,
    "is_serialized": 1
  },
  "sale": null
}
```

**Response format (Product Barcode Match)**:
```json
{
  "success": true,
  "type": "product",
  "status": "available",
  "product": {
    "id": 15,
    "name": "Cat6 UTP Patch Cord 2M",
    "model": "NCB-5EUBLR-02",
    "barcode": "8901234567890",
    "stock": 140,
    "warranty_days": 30,
    "selling_price": 250.00,
    "is_serialized": 0
  }
}
```
