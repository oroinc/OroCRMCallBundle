#  Oro\Bundle\CallBundle\Entity\Call

## ACTIONS  

### get

Retrieve a specific call record.

{@inheritdoc}

### get_list

Retrieve a collection of call records.

{@inheritdoc}

### create

Create a new call record.

The created record is returned in the response.

{@inheritdoc}

{@request:json_api}
Example:

```JSON
{
   "data": {
      "type": "calls",
      "attributes": {
         "subject": "Cold Call",
         "phoneNumber": "229-407-9032",
         "callDateTime": "2016-11-21T13:33:49Z",
         "duration": "217"
      },
      "relationships": {
         "owner": {
            "data": {
               "type": "users",
               "id": "2"
            }
         },
         "callStatus": {
            "data": {
               "type": "callstatuses",
               "id": "completed"
            }
         },
         "direction": {
            "data": {
               "type": "calldirections",
               "id": "outgoing"
            }
         },
         "organization": {
            "data": {
               "type": "organizations",
               "id": "1"
            }
         }
      }
   }
}
```
{@/request}

### update

Edit a specific call record.

The updated record is returned in the response.

{@inheritdoc}

{@request:json_api}
Example:

```JSON
{
   "data": {
      "type": "calls",
      "id": "103",
      "attributes": {
         "subject": "Cold Call",
         "phoneNumber": "229-407-9032",
         "callDateTime": "2016-11-21T13:33:49Z",
         "duration": "217"
      },
      "relationships": {
         "owner": {
            "data": {
               "type": "users",
               "id": "2"
            }
         },
         "callStatus": {
            "data": {
               "type": "callstatuses",
               "id": "completed"
            }
         },
         "direction": {
            "data": {
               "type": "calldirections",
               "id": "outgoing"
            }
         },
         "organization": {
            "data": {
               "type": "organizations",
               "id": "1"
            }
         }
      }
   }
}
```
{@/request}

### delete

Delete a specific call record.

{@inheritdoc}

### delete_list

Delete a collection of call records.

{@inheritdoc}

## FIELDS

### subject

#### create

{@inheritdoc}

**The required field.**

#### update

{@inheritdoc}

**This field must not be empty, if it is passed.**

### phoneNumber

#### create

{@inheritdoc}

**The required field.**

#### update

{@inheritdoc}

**This field must not be empty, if it is passed.**

### callStatus

#### create

{@inheritdoc}

**The required field.**

#### update

{@inheritdoc}

**This field must not be empty, if it is passed.**

### direction

#### create

{@inheritdoc}

**The required field.**

#### update

{@inheritdoc}

**This field must not be empty, if it is passed.**

### callDateTime

#### create

{@inheritdoc}

**The required field.**

#### update

{@inheritdoc}

**This field must not be empty, if it is passed.**

## SUBRESOURCES

### callStatus

#### get_subresource

Retrieve the record of the status configured for a specific call.

#### get_relationship

Retrieve the ID of the status configured for a specific call.

#### update_relationship

Replace the status configured for a specific call.

{@request:json_api}
Example:

```JSON
{
  "data": {
    "type": "callstatuses",
    "id": "completed"
  }
}
```
{@/request}

### direction

#### get_subresource

Retrieve the record of the direction (outgoing, incoming) configured for a specific call.

#### get_relationship

Retrieve the ID of the direction configured for a specific call.

#### update_relationship

Replace the direction configured for a specific call.

{@request:json_api}
Example:

```JSON
{
  "data": {
    "type": "calldirections",
    "id": "outgoing"
  }
}
```
{@/request}

### organization

#### get_subresource

Retrieve the record of the organization a specific call record belongs to.

#### get_relationship

Retrieve the ID of the organization that a specific call belongs to.

#### update_relationship

Replace the organization that a specific call belongs to.

{@request:json_api}
Example:

```JSON
{
  "data": {
    "type": "organizations",
    "id": "1"
  }
}
```
{@/request}

### owner

#### get_subresource

Retrieve the records of the user who is an owner of a specific call record.

#### get_relationship

Retrieve the ID of the user who is an owner of a specific call record.

#### update_relationship

Replace the owner of a specific call record.

{@request:json_api}
Example:

```JSON
{
  "data": {
    "type": "users",
    "id": "2"
  }
}
```
{@/request}


# Oro\Bundle\CallBundle\Entity\CallDirection

## ACTIONS

### get

Retrieve a specific call direction record.

{@inheritdoc}

### get_list

Retrieve a collection of call direction records.

{@inheritdoc}


# Oro\Bundle\CallBundle\Entity\CallStatus

## ACTIONS

### get

Retrieve a specific call status record.

{@inheritdoc}

### get_list

Retrieve a collection of call status records.

{@inheritdoc}
