https://www.cardmarket.com/en/Magic api. Read their [api documents](https://www.mkmapi.eu/ws/documentation) :)

## Files include and descriptions

  

**With [index.php](https://github.com/giventofly/mkmapi-php/blob/master/index.php  "index.php"):**

* use as index.php?card=NAME TO SEARCH
* edit index.php with your api credentials
* see cards matching your query

  

**with [articles-example.php](https://github.com/giventofly/mkmapi-php/blob/master/articles-example.php  "articles-example.php"):**

- edit with your credentials and it will show at max 2 articles from the

user karmacrow, use it as your baseline to other public api queries.

**with [get-card-price.php](https://github.com/giventofly/mkmapi-php/blob/master/get-card-price.php  "get-card-price.php"):**

- you give the card name and edition and get the info on it (including price), for development/production i would separate the request and cache the card id to use in subsequent requests (instead of querying always 2 times to get the info).
- it will use the product id (the other option was the metaproduct id change to your needs).
- Get card productid:

    `$cardid = getMCMinfo('tarmogoyf','Future Sight');`  
    *(returns an array with card id)*
    
- get card price:

    `$cardprice = getMCMinfo(null,null,1452);`
  *(bear in mind it returns an array (check code/cardmarket api for the returned values).*

### TODO:
- optmize code / requests


#### Shameless plug:
Since you like mtg you might like my  [mtg card tooltip plugin](https://github.com/giventofly/MTG-Tooltip-Js) (vanilla javascript with some good options to show the card on mouse over)
