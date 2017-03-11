# Flight Records

### Add Flight Record

* **URL**: api/add\_flight\_record.php
* **Method**: POST
* **Description**: Add a flight record
* **Upstream Data**

  ```js
  {
    user_id: 1,
    date: '2017-01-05',
    flight_num: 'AA288',
    ...
  }
  ```

* **Downstream Data**

  ```js
  {
    status: 'success'
  }
  {
    status: 'fail',
    message: ''
  }
  ```

### Get All Flight Records

* **URL**: api/get\_flight\_records.php
* **Method**: GET
* **Description**: Get a user's all flight records
* **Upstream Data**

  ```js
  {
    user_id: 1
  }
  ```

* **Downstream Data**

  ```js
  {
    status: 'success',
    data: [{
      flight_record_id: 12,
      date: '2017-01-12',
      flight_num: 'AA288',
      ...
    }]
  }
  {
    status: 'fail',
    message: ''
  }
  ```
  
### Update A Flight Record

* **URL**: api/update\_flight\_record.php
* **Method**: POST
* **Description**: Update a flight record
* **Upstream Data**

  ```js
  {
    flight_record_id: 1,
    date: '2017-01-05',
    flight_num: 'AA288',
    ...
  }
  ```

* **Downstream Data**

  ```js
  {
    status: 'success'
  }
  {
    status: 'fail',
    message: ''
  }
  ```
  
### Delete A Flight Record

* **URL**: api/delete\_flight\_record.php
* **Method**: POST
* **Description**: Add a flight record
* **Upstream Data**

  ```js
  {
    flight_record_id: 1
  }
  ```

* **Downstream Data**

  ```js
  {
    status: 'success'
  }
  {
    status: 'fail',
    message: ''
  }
  ```










