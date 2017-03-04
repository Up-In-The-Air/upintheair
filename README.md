# Up in the Air

> Flight collection, analysis, social and more!

Site Url: http://upintheair.web.engr.illinois.edu/

## Development

  - Install Dependencies

    ``` bash
    $ npm install
    ```

  - Run a php dev server on localhost. You can specify the port.

    ``` bash
    $ php -S localhost:8000
    ```

## Deployment

  - Commit and push your changes.

  - `ssh` on cpanel

    ``` bash
    $ ssh <NetID>@webhost.engr.illinois.edu
    ```

  - Switch to `upintheair` user.

    ``` bash
    $ sudo -i -u upintheair
    ```

  - In `public_html` directory, pull the updates.

    ``` bash
    $ git pull
    ```

    Now you are all set!
