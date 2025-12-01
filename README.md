
## Task#4
<div align="center">
  <img src="https://media.giphy.com/media/dWesBcTLavkZuG35MI/giphy.gif" width="600" height="300"/>
</div>

### task#4App
#### ТЗ ---> Регистрация:

* name + email + password
* hash
* status=unverified
* письмо асинхронно

#### Verify email
* меняет на active
* blocked остаётся blocked

$$$$ Авторизация
* unverified может входить
* blocked НЕ может
* deleted = может заново регистрироваться

#### Админ-панель
* таблица
* чекбоксы
* toolbar
* NO кнопок в строках
* сортировка
#### Bulk-actions
* block
* unblock
* delete
* delete_unverified
* self-block → logout