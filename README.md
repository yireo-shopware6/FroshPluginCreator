# Frosh Plugin Creator
This plugin enhances the Shopware 6 command `plugin:create`

## Installation

    composer config repositories.froshPluginCreate vcs git@github.com:yireo-shopware6/FroshPluginCreator.git
    composer require frosh/plugin-creator
    bin/console plugin:refresh
    bin/console plugin:install --activate FroshPluginCreator

## Usage
The default behaviour is still as usual:

    bin/console plugin:create SwagExample

However, we do now have additional flags:

    bin/console plugin:create SwagExample --namespace 'Swag\Example' --composer-name 'swag/shopware6-example' --description 'My Swag Example'

Make sure to be careful with the `--namespace` option: The backslash should be escaped. If you are using double quotes, the backslash needs to be a double-backslash (`Swag\\Example`).
