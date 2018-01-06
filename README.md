## 專案架構
1. 參考 http://oomusou.io/laravel/laravel-architecture/ 去實作

## 資料夾
1. resources/view 放置html
2. (非原生,自己新增)app/Http/Presenters/comment 放置前端判斷邏輯的function
3. public/js/comment 放置javascript
4. routes/web.php 設定URL
5. app/Http/Controllers 放置controller，接view送過來的值(controller會呼叫各種service function)
6. (非原生,自己新增)app/Http/Services 功能實作與邏輯判斷、呼叫對應的repositories function
7. (非原生,自己新增)app/Http/Repositories 各種select, insert, update, delete function，每個檔案對應到一個資料表

## 使用套件
1. Laravel Collective https://laravelcollective.com/docs/master/html 
2. laravel debugbar (上線時將.env設成APP_DEBUG=false, 上線環境就不會出現debug模式)
3. 更換icon圖示 使用 http://fontawesome.io/icons/#new

## 資料庫設定
1. .env (下載git後，複製.env.example的檔案，修改成自己的資料庫帳密)
2. config/database.php (可設定DB讀寫分離)

## 使用到的DB與資料表
1. db_cms
    - series
2. db_renta
    - book_comment
    - book_comment_black_list
    - book_comment_praise
    - book_comment_reply
    - book_comment_report
    - t_hist
    - user_score
3. db_user
    - t_account
