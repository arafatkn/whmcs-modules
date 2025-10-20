# WHMCS Coupon Duplicator

**Version:** 1.0  
**Author:** Arafat Islam

Duplicate WHMCS promotion coupons multiple times with either random codes or a list of custom codes, while preserving all original coupon settings.

---

## Features

- Duplicate an existing coupon by its ID.
- Generate multiple new coupons with:
    - Random codes, with optional prefix and suffix.
    - Custom codes provided as a list.
- Automatically skips duplicate codes.
- Shows a success list of newly created coupons with **Edit** links for easy modification.
- Admin-friendly UI with a clear warning when custom codes are provided.

---

## Installation

1. Upload the `coupon_duplicator` folder to your WHMCS installation's addons directory:

    ```
    /modules/addons/coupon_duplicator/
    ```
2. Ensure the file permissions are set correctly for WHMCS to access the module.
3. Log in to your WHMCS Admin Panel.
4. In WHMCS Admin Panel, go to:  
   `System Settings → Addon Modules`
5. Activate **Coupon Duplicator**.
6. Access the module under:  
   `Addons → Coupon Duplicator`

---

## Usage

1. Enter the **Coupon ID** of the coupon you want to duplicate.
2. Optionally set:
    - **Prefix** for randomly generated codes.
    - **Number of Random Characters** for randomly generated codes.
    - **Suffix** for randomly generated codes.
    - **Number of Copies** (ignored if custom codes are provided).
3. Optionally, enter **Custom Codes** (one per line).  
   ⚠️ **Note:** If custom codes are provided, `Prefix`, `Suffix`, and `Number of Copies` will be ignored.
4. Click **Duplicate Coupon**.
5. View the success list of newly created coupons with **Edit** links for each coupon.

---

## Example

- **Random Codes**
    - Coupon ID: `10`
    - Prefix: `SPRING-`
    - Number of Random Characters: `8`
    - Suffix: `-2025`
    - Number of Copies: `5`

  Will generate 5 coupons like:

      SPRING-1A2B3C4D-2025

- **Custom Codes**  
  Entered in the textarea like:

      SUMMER2025
      FALL2025
      WINTER2025

  Creates exactly these codes, ignoring prefix/suffix/number of copies.

---

## Notes

- The module uses WHMCS database table `tblpromotions`.
- Duplicate coupon codes are automatically skipped.
- Edit links redirect to WHMCS coupon edit page in a new tab.

---

## Changelog

**1.0**
- Initial release: Duplicate coupons with random codes and optional prefix/suffix.
