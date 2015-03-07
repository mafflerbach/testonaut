java -jar E:\xampp\htdocs\phpSelenium\vendor\selenium\selenium-server.jar -role hub
java -jar E:\xampp\htdocs\phpSelenium\vendor\selenium\selenium-server.jar -role node  -hub http://localhost:4444/grid/register -browser browserName=chrome,maxInstances=5,platform=WINDOWS -port 5556
java -jar E:\xampp\htdocs\phpSelenium\vendor\selenium\selenium-server.jar -role node  -hub http://localhost:4444/grid/register -browser browserName=firefox,maxInstances=5,platform=WINDOWS -port 5555
java -jar E:\xampp\htdocs\phpSelenium\vendor\selenium\selenium-server.jar -role node  -hub http://localhost:4444/grid/register -browser browserName=iexplorer,maxInstances=5,platform=WINDOWS -port 5557
