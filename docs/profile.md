# Profile

### Top Airports

* **URL**: api/get\_top\_airports.php
* **Method**: GET
* **Description**: Get top frequent airports
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
      iata: 'JFK',  
      name: 'John F Kennedy',
      city: 'New York',
      frequency: 12
    }, {
      iata: 'PVG',
      name: 'Pudong',
      city: 'Shanghai',
      frequency: 8
    }]
  }
  {
    status: 'fail',
    message: ''
  }
  ```



