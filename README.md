Code can be found at this URL:   https://github.com/hassansg1/chat_app


To setup the project :

Clone the code using following
git clone https://github.com/hassansg1/chat_app

Run composer update

Run php -S localhost:8080 -t public public/index.php to run the project

Go to http://localhost:8080/db_setup if need to setup basic data (already setup in db.sqlite)

API calls :
http://localhost:8080/messages/add 
    to send a new message

http://localhost:8080/messages/2   
    to get messages sent to a user 

http://localhost:8080/getNewMessages/2   
    to get new messages sent to a user in a set interval

