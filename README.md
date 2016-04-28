#Welcome by Testonaut #

## In short: ##
Testonaut is a simple wiki, which you can running selenium test on every selenium node in your infrastructure.

## It's a Collaboration tool ##

it has a very low entry and learning curve, which makes it an excellent tool to  collaborate with, for example, business stakeholders.

## It's a Test tool ##
The wiki pages created in testonaut are run as tests. The specifications can be tested against the application itself,  resulting in a roundtrip between specifications and implementation.

Great software requires collaboration and communication. testonaut is a tool for enhancing collaboration in  software development. It's an invaluable way to collaborate on complicated problems -and get them right- early in  development. Allows customers, testers, and programmers to learn what their software should do and what it does do. It  automatically compares customers' expectations to actual results.

It is an integration testing tool. That means that it provides a method to automatically determine that your  application is working correctly. Not your beautiful user interface, with all its fancy CSS and slick Ajax calls, but  the stuff underneath, where the actual brains of the application live. 

The goal is for testonaut to operate at a level just below the user interface level, demonstrating that, given various  inputs to your application, the correct results are returned. In a sense, you could consider it an alternative user  interface for the application.

The best part of all - it runs on your existing selenium infrastructure.


---------------------------------------
## Features ##

* using the standard output from selenium IDE
* workaround for full capturing pages in chrome (https://bugs.chromium.org/p/chromedriver/issues/detail?id=294)
* Different Browser profiles i.e:
    * using chrome mobile device emulator
    * different window width and height
* different base url settings for every profile
* automated screenshot comparison
* screenshot overview over all tests in project or suite pages
    * screenshot filter (all | fail | success)
* automated selenium node recognition
* simple html wysiwyg
* possible call via commandline
* jenkins compatible output
* build history
* ldap support
* simple usermanagment if you don't have ldap
* versioning via git (pull not implemented yet)
    reverts (btw checkout to previews revisions)
* diff view between revisions;


---------------------------------------
## TODO ##
* implement pull to upstream git repo

---------------------------------------
##Installation ##

Testonaut is still in beta phases, so there is no absolute brain afk installation. There is also some requirements for the runtime.

you need git and an apache installation with php greater then 5.3 with additional modules ldap, fileinfo and imagemagick. For dependencies installation you need a composer too.

    git clone https://github.com/mafflerbach/testonaut.git
    composer install

Under http://youre.installation/*/globalconfig/
You can set your domain to your selenium hub.

The definition of "app path" defining your subpath on your apache DOCUMENT_ROOT, if you don't like to use subdomains.

---------------------------------------

##Get Started ##
Testonaut needs a selenium infrastructure. You configure the selenium hub addresse and all tests can be run on your selenium installation.
Everyone who must working with Integration tests, should install the firefox extension Selenium IDE. It's a capture replay tool for your frontendintegration tests.

* Capture a test with the Selenium IDE and save it on your disk
* Open these test in a normal Texteditor.    
* copy and paste the test in Testonaut.    
* save the page, and confige the page as testpage.  

After these steps, you can run your test on every selenium node.

---------------------------------------

##Pagesettings ##
There are four types of page types. static, suite, test and project pages.
* static pages were skipped in test runs.
* suite pages runs amount of test pages which runs automatically.
test pages contains all information about a test and the test itself.

* project pages contains only some project informations, but they differently in the configuration.
In project and suite pages you can configure node specific base url for every selenium node.

This is interesting for paralell test runs.

##Screenshot settings ##
Besides being able to use the command *captureEntirePageScreenshot* at any time, you have opportunity taking screenshot automatically.
This can be defined  after each test or after every single command.

---------------------------------------
##Header and Footer ##

There are three types of header and footers.

page header and footer, test header and footer and the last one, suite header and footer. pages with titles *pageHeader* and *pageFooter* are include automaticaly in actual page context.
*setUp* and *tearDown* pages are only includes in pages from type *test*
*suiteSetUp* and *suitTearDown* are only includes in pages from type *Suite*
If the included pages have selenium testcases, so they will run as a normal test.
At this point you must look out at the "close command" in included files, otherwise all upcomming tests where failing.

---------------------------------------

##Running tests ##
If you have set your configuration right the single wiki page must be configurate as *test*,*suite* or *project page*.
After this settings change, you can use the run button on yout wikipage and select your browser flavour.

---------------------------------------
##Working with Browserprofiles ##

Testronaut provides the possibility to apply different browser profile. So you can save different settings for each browser.
#### Firefox / Internet Explorer

You can set different height and width of the browser window.
#### Chrome
Chrome has the additional possibility to emulate different mobile devices. (Https://developer.chrome.com/devtools/docs/device-mode)
The list of supported devices can be selected via drop down menu.

---------------------------------------

## Import your Tests ##

You can import your existing (html) selenium zip via zip File. Set Your Page as type 'Project'

You order your Files e.g

    importTest
      ├ foo.html
      └ baa
        └ fobaa.html

And you get in your wiki:

    importTest
      ├ foo
      └ baa
        └ fobaa

---------------------------------------
## Screenshot 
comparisons are quite easy to implement, they are done almost automatically.
In selenium there is the command *captureEntirePageScreenshot*. The second parameter specifies the imagefilename. 
If a reference image exists, it will be automatically compared with the reference image.
You can find all Screenshot comparisons under the "screenshot" section.
The first column displays the recently captured image. The second column displays the reference image.
The third column displays the diff image, it will mark all differences in red 

By clicking on the X, the images will be *deleted*. 
In the first picture there is also a checkmark. This action allows you to replace the current reference Image with the image *recently captured*.
        
---------------------------------------

##"Quicksetup" ##

with a bunch of internet explorers on your machine:

Preset you need a virtualbox installation.
Get your modernIE maschine from www.modern.ie
On your maschine download: http://selenium-release.storage.googleapis.com/2.44/selenium-server-standalone-2.44.0.jar

this is our hub. If you have downloaded start the hub with
java -jar path/to/jar/selenium-server-standalone-2.44.jar -role hub

You must install a java jdk. for the second time you download the selenium server in your VM.
You must download under http://selenium-release.storage.googleapis.com/index.html?path=2.44/ the IEDriverServer
after installation from Java and selenium go in your cmd in the jdk install dir e.g. ( cd "C:\Program Files\Java\jdk1.8.0_25\bin")

you should customize the version number, path and Hub ip ;)

* The IEDriverServer exectuable must be downloaded and placed in your PATH.
* On IE 7 or higher on Windows Vista or Windows 7, you must set the Protected Mode settings for each zone to be the same value. The value can be on or off, as long as it is the same for every zone. To set the Protected Mode settings, choose "Internet Options..." from the Tools menu, and click on the Security tab. For each zone, there will be a check box at the bottom of the tab labeled "Enable Protected Mode".
* Additionally, "Enhanced Protected Mode" must be disabled for IE 10 and higher. This option is found in the Advanced tab of the Internet Options dialog.
* The browser zoom level must be set to 100% so that the native mouse events can be set to the correct coordinates.
* For IE 11 only, you will need to set a registry entry on the target computer so that the driver can maintain a connection to the instance of Internet Explorer it creates. For 32-bit Windows installations, the key you must examine in the registry editor is HKEY_LOCAL_MACHINE\SOFTWARE\Microsoft\Internet Explorer\Main\FeatureControl\FEATURE_BFCACHE. For 64-bit Windows installations, the key is HKEY_LOCAL_MACHINE\SOFTWARE\Wow6432Node\Microsoft\Internet Explorer\Main\FeatureControl\FEATURE_BFCACHE. Please note that the FEATURE_BFCACHE subkey may or may not be present, and should be created if it is not present. Important: Inside this key, create a DWORD value named iexplore.exe with the value of 0.

After installation from Java and selenium

    cd "path/to/jdk/install/dir/bin"
    java -jar path/to/selenium-server.jar -role node -hub {YOUR-HUB-IP}:4444/grid/register -Dwebdriver.ie.driver="path/to/IEDriverServer.exe" -browser browserName="iexplore",version={version},platform="WINDOWS",maxInstances=5

you should customize the version number and Hub ip ;) 

##Jenkins setup ##

You need the HTTP Request Plugin: https://wiki.jenkins-ci.org/display/JENKINS/HTTP+Request+Plugin

and also the HTML Publisher Plugin: https://wiki.jenkins-ci.org/display/JENKINS/HTML+Publisher+Plugin

Add a free style Job

Copy the run URL from your Test from testonaut

Add Build step "HTTP Request"

Paste the test URL

set the option "Response content expected" to "Build: Success"

set the option "Output response to file" to index.html

Add Post build action "Publish HTML reports"

Set option "Index page[s]" to index.html


---------------------------------------

#planned features....

* implementing variables
* Templates for pagepresets in editmode

##modules

* Pagecrawler for generating a testing tree
* testgenerator for Equivalence classes analysis
* checklist for Accessibility (Bitv) (inkl Prio)

---------------------------------------
