HTML menu creation for Laravel Blade
====================================

This allows you to define your HTML menu system using easy to read syntax in blade file.

It allows the menu urls to change if it's called from an admin directory. ie.
It currently only supports tabs (not spaces).

Installing
==========

Add the dependency to your project:

```bash
composer require DNABeast/tabmenu:dev-master
```

After updating composer, add the ServiceProvider to the providers array in config/app.php

### Laravel 5.2:

```
DNABeast\TabMenu\TabMenuServiceProvider::class,
```

You may need to clear the view cache
```
php artisan view:clear
```

Usage
=====

In your blade file enter the custom directive start and end points with your menu a text list

```
@menu
[tab][tab]Menu 1
[tab][tab]Menu 2
[tab][tab]Menu 3
@endmenu
```

outputs

```
<ul>
	<li><a href="/menu-1">Menu 1</a></li>
	<li><a href="/menu-2">Menu 2</a></li>
	<li><a href="/menu-3">Menu 3</a></li>
</ul>
```

### Submenus

Add a tab and the menu item will be come a submenu.

```
@menu
[tab][tab]Menu 1
[tab][tab][tab]Menu 1a
[tab][tab][tab][tab]Menu 1ax
[tab][tab]Menu 2
@endmenu
```
creates
```
<ul>
	<li><a href="/menu-1">Menu 1</a><ul>
		<li><a href="/menu-1a">Menu 1a</a><ul>
			<li><a href="/menu-1ax">Menu 1ax</a></li></ul>
		</li></ul>
	</li>
	<li><a href="/menu-2">Menu 2</a></li>
</ul>
```

### set URLs

Put a comma and set the url when it's different to the menu name

```
@menu
Menu, /menu-one-location
Menu 2
@endmenu
```
becomes
```
<ul>
	<li><a href="/menu-one-location">Menu</a></li>
	<li><a href="/menu-2">Menu 2</a></li>
</ul>
```

### Set a Class

If your menu item requires a class name just add it after a second comma

```
@menu
Menu Item, /menu-item, action
@endmenu
```
becomes
```
<ul>
	<li><a href="/menu-item" class="action">Menu Item</a></li>
</ul>
```