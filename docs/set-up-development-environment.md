# Set Up Development Environment

1. For this simple site, we use just one repository for all our client-side and server-side codes/scripts. So the first thing to do for development is `clone` or `pull` the repository from [GitHub](https://github.com/Up-In-The-Air/upintheair)

    ```bash
    $ git clone https://github.com/Up-In-The-Air/upintheair.git
    ```

1. To make our front-end stuff well-organized and easy to go, we need **[npm](https://www.npmjs.com)**. Configuration package is already included in the project. We just need to install the dependencies.

    ```bash
    $ npm install
    ```
    
1. This is a database-based project, so we need to set up a server connected with a database during our development. Due to the MySQL in cPanel not being allow to be connected remotely, it's better for us install MySQL locally.

    - Download MySQL [here](https://dev.mysql.com/downloads/mysql/).
    
    - After you got this done, you need to create a database called *upintheair_default* and a user *upintheair_admin* with password *admin411*. NOTE: These names are **REQUIRED** as we already use them on cPanel. Using the same configurations will benefit a lot when deploying.
    - To administrate your local MySQL database, you can take advantage of `mysql` CLI, like
    
    ```bash
    # Log in your local MySQL server with root privilage
    $ mysql -h localhost -u root -p
    ```
 
    or
    
    ```bash
    # Log in with a common user after you setup
    $ mysql -h localhost -u [username] -p
    ```
    
    You can refer to [this tutorial](https://www.digitalocean.com/community/tutorials/how-to-create-a-new-user-and-grant-permissions-in-mysql).
    
    - An alternative (and I recommended) way is use database administrative softwares like [Sequel Pro](https://www.sequelpro.com/). You can get this down easier with the GUI tool.
    
1. You can test you PHP code with PHP Repl, an interactive way of learning/exploring a new language. It is pre-installed with PHP 5.0+.

    ```bash
    $ php -a
    Interactive Shell
    
    php >
    ```
    
    Test simple sentences:
    
    ```php
    php > echo 3 + 7;
    10
    ```
    
    You can also test connection and SQL with your local MySQL database.
    
    ```php
    php > $conn = new mysqli('localhost', 'upintheair_admin', 'admin411'); 
    ```

1. Run your development server.

    ```bash
    # Port can be specified
    $ php -S localhost:8000
    ```
    
    Then you are all set!    



