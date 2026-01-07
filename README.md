# Requirements

Project: Simple E-commerce Shopping Cart

Create a project for a simple e-commerce shopping cart system. Users should be able to browse products, add them to a cart, update quantities, and remove items. Each product should have a name, price, and stock_quantity.

Each shopping cart must be associated with the authenticated user (User model).
When a user adds products to their cart, updates quantities, or removes items, these actions should be stored and retrieved based on the currently authenticated user (not via session or local storage).
Make sure to use Laravel’s built-in authentication from the starter kit.

Keep it simple and follow laravel best practices and guidelines.

Tech Stack:

Backend: Laravel

Frontend: React, Vue or Livewire

Styling: Tailwind CSS

Version Control: Git/GitHub

Key Requirements:

Low Stock Notification: When a product's stock is running low, a Laravel Job/Queue should be triggered to send an email to a dummy admin user.

Daily Sales Report: Implement a scheduled job (cron) that runs every evening and sends a report of all products sold that day to the email of the dummy admin user.

Submission: Please host the code on GitHub and add @dylanmichaelryan as a collaborator to the repository.
