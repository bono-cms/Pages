
Pages module
============

The **Pages module** is a powerful tool for managing and customizing pages on your website. It enables you to create custom pages and assign unique templates and controllers to each, providing full flexibility in design and functionality. With this module, you can structure your website efficiently, ensuring seamless integration between content, templates, and backend logic.

# Templates

This module comes with three default templates for managing different types of pages.

## Home page
All logic related to the home page is handled within the `pages-home.phtml` template.

**TIP:** To check in the layout whether the current page is the home page, use the `$page->getDefault()` method globally.

## Default template
The default template is `pages-page.phtml`

Basic example:

    <article>
	    <h1><?= $page->getName(); ?></h1>
	    <?= $page->getContent(); ?>
    </article>

## 404 Template
The `pages-404.phtml` template is rendered when a visitor tries to access a page that does not exist on your website.

Basic example:

    <section class="py-5 my-5">
    	<div class="container py-4">
    		<h1>404: <?= $page->getName(); ?></h1>
            <p><?= $page->getContent(); ?>
    	</div>
    </section>

# Available methods
The following methods can be used to retrieve page-related data:

    $page->getName(); // Returns page name
    $page->getTitle();  // Returns page title
    $page->getContent(); // Returns page content
    $page->getDefault(); // Check whether this is home page
    $page->getUrl(); // Returns current page URL

# Custom Fields

Custom fields are available via Block module. Once a group of custom fields created and attached to your page, you get values of custome fields via `getField()`  method, like this:

    <p><?= $page->getField(123); ?></p>