java -jar E:\xampp\htdocs\phpSelenium\vendor\selenium\selenium-server.jar -role hub
java -jar E:\xampp\htdocs\phpSelenium\vendor\selenium\selenium-server.jar -role node  -hub http://localhost:4444/grid/register -browser browserName=firefox,maxInstances=5,platform=WINDOWS -port 5555
java -jar E:\xampp\htdocs\phpSelenium\vendor\selenium\selenium-server.jar -role node  -hub http://localhost:4444/grid/register -Dwebdriver.chrome.driver="E:\xampp\htdocs\phpSelenium\vendor\driver\chromedriver.exe" -browser browserName=chrome,maxInstances=5,platform=WINDOWS -port 5556


java -jar E:\xampp\htdocs\phpSelenium\vendor\selenium\selenium-server.jar -role node  -hub http://localhost:4444/grid/register -Dwebdriver.ie.driver="E:\xampp\htdocs\phpSelenium\vendor\driver\IEDriverServer.exe" -Die.forceCreateProcessApi=true -browser browserName=iexplore,maxInstances=5,platform=WINDOWS -port 5557



rem %1 name of the IE version

mkdir C:\tmp
cd C:\tmp\
bitsadmin.exe /transfer "Download Selenium" http://selenium-release.storage.googleapis.com/2.44/selenium-server-standalone-2.44.0.jar c:\tmp\selenium-server.jar
bitsadmin.exe /transfer "Download IEDriver" http://selenium-hub.dim/downloads/IEDriverServer.exe c:\tmp\IEDriverServer.exe
bitsadmin.exe /transfer "Download Java" http://selenium-hub.dim/downloads/jdk-8u25-windows-i586.exe c:\tmp\jdk.exe
bitsadmin.exe /transfer "Download selenium bat" http://selenium-hub.dim/seleniumWinInstall/selenium.bat c:\tmp\selenium.bat

c:\tmp\jdk.exe /s
c:\tmp\selenium.bat
----

Set-Location HKLM:\Software\Microsoft\Windows\CurrentVersion\Policies\system\
New-ItemProperty -Name LocalAccountTokenFilterPolicy -PropertyType dword -path . -Value 1
Set-ItemProperty -Name EnableLUA -path . -Value 0
shutdown -t 0 -r -f

bitsadmin.exe /transfer "Download preinstall bat" http://selenium-hub.dim/seleniumWinInstall/preinstalls.bat c:\tmp\preinstalls.bat



cd "C:\Program Files\Java\jdk1.8.0_25\bin"
java -jar c:\tmp\selenium-server.jar -role node -hub http://192.168.50.136:4444/grid/register -Dwebdriver.ie.driver="c:\tmp\IEDriverServer.exe" -browser browserName="iexplore",version=10,platform="WINDOWS",maxInstances=5java -jar E:\xampp\htdocs\phpSelenium\vendor\selenium\selenium-server.jar -role node  -hub http://localhost:4444/grid/register -Dwebdriver.ie.driver="E:\xampp\htdocs\phpSelenium\vendor\driver\IEDriverServer.exe" -Die.forceCreateProcessApi=true -browser browserName=iexplore ,maxInstances=5,platform=WINDOWS -port 5557
java -jar E:\xampp\htdocs\phpSelenium\vendor\selenium\selenium-server.jar -role node  -hub http://localhost:4444/grid/register -Dwebdriver.chrome.driver="E:\xampp\htdocs\phpSelenium\vendor\driver\chromedriver.exe" -Dwebdriver.ie.driver="E:\xampp\htdocs\phpSelenium\vendor\driver\IEDriverServer.exe" -Die.forceCreateProcessApi=true -nodeConfig E:\xampp\htdocs\phpSelenium\capabilities.json
