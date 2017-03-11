# Sign Up/Log In/Log Out

### Sign Up

* URL: api/sign\_up.php
* Method: POST
* Description: Sign up a new user
* Upstream Data

  ```js
  {
    first_name: '',
    last_name: '',
    email: '',
    password: '',
    confirmed_password: ''
  }
  ```

* Downstream Data

  ```js
  {
    status: 'success',
    data: {
      'first_name': '',
      'last_name': '',
      'email': '',
      'id': 1,
      'cookie': {
        'id': 1,
        'key': '',
        'expires': 1111111111
      }
    }
  }
  {
    status: 'fail',
    message: ''
  }
  ```

### Log In

* URL: api/log\_in.php
* Method: POST
* Description: Log in a user
* Upstream Data

  ```js
  {
    email: '',
    password: '',
    auto_login: true
  }
  ```

* Downstream Data

  ```js
  {
    status: 'success',
    data: {
      'first_name': '',
      'last_name': '',
      'email': '',
      'id': 1,
      'cookie': {
        'id': 1,
        'key': '',
        'expires': 1111111111
      }
    }
  }
  {
    status: 'fail',
    message: ''
  }
  ```

### Auto Log In

* URL: api/auto\_login.php
* Method: POST
* Description: Auto log in a user with cookie/session
* Upstream Data

  ```js
  {
    id: 1,
    key: ''
  }
  ```

* Downstream Data

  ```js
  {
    status: 'success',
    data: {
      'first_name': '',
      'last_name': '',
      'email': '',
      'id': 1
    }
  }
  {
    status: 'fail',
    message: ''
  }
  ```

### Log Out

* URL: api/log\_out.php
* Method: POST
* Description: Log out a user
* Upstream Data

  ```js
  {
    id: 1
  }
  ```

* Downstream Data

  ```js
  {
    status: 'success'
  }
  {
    status: 'fail',
    message: ''
  }
  ```









