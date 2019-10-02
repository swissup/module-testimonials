# Testimonials

## Installation

### For clients

There are several ways to install extension for clients:

 1. If you've bought the product at Magento's Marketplace - use
    [Marketplace installation instructions](https://docs.magento.com/marketplace/user_guide/buyers/install-extension.html)
 2. Otherwise, you have two options:
    - Install the sources directly from [our repository](https://docs.swissuplabs.com/m2/extensions/testimonials/installation/composer/) - **recommended**
    - Download archive and use [manual installation](https://docs.swissuplabs.com/m2/extensions/testimonials/installation/manual/)

### For developers

Use this approach if you have access to our private repositories!

```bash
cd <magento_root>
composer config repositories.swissup composer https://docs.swissuplabs.com/packages/
composer require swissup/module-testimonials --prefer-source
git checkout master
bin/magento module:enable Swissup_Testimonials
bin/magento setup:upgrade
```
