# phalconModel

There are some issues while saving models in Phalcon v.4.0-beta2.

1. Fields have to be called with underscore, not in camelCase (in camelCase will return a "field not found")
2. Models are not working when updating data (haven't tested if there are issues when creating).

## Steps to reproduce

1. Create the test database (Run `demo.sql`) and provide db connection details in index.php
2. Visit the endpoints in the app:

```
// Show all records
http://localhost/phalconModel/search

// Show Jane
http://localhost/phalconModel/show/2

// The app will update user 1 only

// Raw update that will effectively change the name of John to Johnny
http://localhost/phalconModel/updateRaw/Johnny

// First attempt to update using model following example in documentation
http://localhost/phalconModel/saveModel/Maggie

// Second attempt to update, now with confidence
http://localhost/phalconModel/updateModel/Maggie

// Third attempt to update, using the findFirst technique that used to work in v3: 
// visit the endpoint to try it (only in Phalcon 3.x):
http://localhost/phalconModel/saveFindFirst/Maggie

// Similar method adapted to v4 reports that the record was saved. 
// However, it wasn't. MySQL log shows that Phalcon re-saved the record that it found first, 
// disregarding the update data.
http://localhost/phalconModel/updateModelFindFirst/Maggie

// Fourth attempt to update, with confidence and whitelisting two fields.
// Fails and this test will not even fire notSaved() event
http://localhost/phalconModel/updateModelWhitelist/Maggie

```

save() and update() are not working as expected. In all the failed attempts (except when using findFirst as in v3), it seems that Phalcon is trying to create a new record, but refuses since mandatory field "password" is not present. Viewing the sql logs, the issue could be that `id` is not being recognized --which might explain why similar cases fail in v3, forcing to use findFirst before update/save.

