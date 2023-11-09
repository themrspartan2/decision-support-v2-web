Description of files included:
	cacert.pem: This file allows API calls to be made by php. Place this file in a location that will not change
				and paste the path to this file into the included php.ini file
				
	php.ini:	This is the file that will allow php to run on your system. Before using it though, open
				it in a text editor and use ctrl+f to find the term "<PATH/TO/CACERT.PEM>". Replace this
				term with the path to where you placed cacert.pem. After this, copy php.ini into the same
				directory as php.exe (the location where you installed php).