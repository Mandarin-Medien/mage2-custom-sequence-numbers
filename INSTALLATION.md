# Custom Sequence Numbers

## 1. Requirements

* This module requires Magento 2.2 or 2.3

## 2. Installation

### 2.1 Manual installation from ZIP file

1. Extract the contents of the ZIP file into a temporary directory on your system

2. Create the directory `app/code/MandarinMedien/CustomSequenceNumbers/` in your Magento2 installation directory.

3. Copy all the extracted module files into this newly created directory.

4. Enable the module according to **section 2.3**

   

### 2.2 Installation via Composer

1. Run the following command in your terminal:
   `composer require mandarinmedien/module-customsequencenumbers`
2. Enable the module according to **section 2.3**

### 2.3 Enabling the module

Run the following commands in your terminal to enable the module:

1. `php bin/magento module:enable MandarinMedien_CustomSequenceNumbers`
2. `php bin/magento setup:upgrade`
3. `php bin/magento cache:clean`
