# Custom Sequence Numbers

This module replaces the classic numbers of order related data sets
(like orders, invoices, credit memos and shippings) by an incrmenting number
with an prefixed date reference.

## What this module does

- The default order number will be replaced with the format `YYMMDD####`.
- Where `####` represents a 4-digit counter, which increments per every new
  data set.
- The 4-digit counter will be reset to `0001` with the beginning of every new
  year.
- `YYMMDD` will be replaced by the current date (year - month - day).
- The 6-digit date part is not affecting the counter. 

## Installation & Configuration

The module has no own configuration and is usable directly after installation.

---

## Technical Information

### How it works

- The last used year is stored in a flag.
- If the stored year is different from the current year the counter will be
  reset.  
  To do this, the `start_value` of the related sequence profile will be set to
  the  current sequence value.
- To allow this "easy" reset, the calculation method was replaced by an
  optimized (more "logical") one.  

### Technical changes made by the module

- Stores flag `customsequencenumbers` with formats to update and reset increment 
  id parts.
- Changes default pattern of increment IDs in 
  `Magento\Framework\DB\Sequence\SequenceInterface` to have only 4 digits.
- Replaces `Magento\SalesSequence\Model\Sequence` to allow custom way to
  calculate new order increment values.

```
@author   : MANDARIN MEDIEN G.f.d.L. mbH <shop@mandarin-medien.de>
@copyright: 2019 MANDARIN MEDIEN G.f.d.L. mbH
@license  : MIT License
@version  : 1.0.0
```
