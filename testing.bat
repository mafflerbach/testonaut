java -jar E:\xampp\htdocs\phpSelenium\vendor\selenium\selenium-server.jar -role hub
java -jar E:\xampp\htdocs\phpSelenium\vendor\selenium\selenium-server.jar -role node  -hub http://localhost:4444/grid/register -browser browserName=firefox,maxInstances=5,platform=WINDOWS -port 5555
java -jar E:\xampp\htdocs\phpSelenium\vendor\selenium\selenium-server.jar -role node  -hub http://localhost:4444/grid/register -Dwebdriver.chrome.driver="E:\xampp\htdocs\phpSelenium\vendor\driver\chromedriver.exe" -browser browserName=chrome,maxInstances=5,platform=WINDOWS -port 5556


java -jar E:\xampp\htdocs\phpSelenium\vendor\selenium\selenium-server.jar -role node  -hub http://localhost:4444/grid/register -Dwebdriver.ie.driver="E:\xampp\htdocs\phpSelenium\vendor\driver\IEDriverServer.exe" -Die.forceCreateProcessApi=true -browser browserName=iexplore ,maxInstances=5,platform=WINDOWS -port 5557
java -jar E:\xampp\htdocs\phpSelenium\vendor\selenium\selenium-server.jar -role node  -hub http://localhost:4444/grid/register -Dwebdriver.chrome.driver="E:\xampp\htdocs\phpSelenium\vendor\driver\chromedriver.exe" -Dwebdriver.ie.driver="E:\xampp\htdocs\phpSelenium\vendor\driver\IEDriverServer.exe" -Die.forceCreateProcessApi=true -nodeConfig E:\xampp\htdocs\phpSelenium\capabilities.json
