# Upgrade guide

## Upgrading To 1.1.0

From version 1.1.0 plugin requires October build 300 and above.

## Upgrading To 1.1.4

Added wrapper_class to field properties. Update fields type manually if you want to use this.

## Upgrading To 1.2.7

Plugin will register Contact Form Template and Default Form Template. If you would like to use new mail layout you need to create it manually from the source /plugins/schultenmedia/formbuilder/updates/mail/layouts/formbuilder/. Layout code should be specified as form_builder. Assign newly created layout to the templates by updating them manually.

## Upgrading To 2.0.1

Plugin requires OctoberCMS version 2.x with Laravel 6.x and PHP >=7.3.

Drop support for OctoberCMS version 1.x.

If you upgrade from version 1.5.0, then you should reinstall plugin or apply patch with following command:

```
php artisan formbuilder:patch 2.0
```
