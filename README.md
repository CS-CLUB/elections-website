Elections Website
=================

Description
-----------

Each year at the start of the fall semester the UOIT/DC Computer Science Club has
an election to determine the executives for the current year. This is the source
code for the election website that the club uses for elections each year.

Why is the Elections Website Open Source?
-----------------------------------------

Unlike traditional closed-source proprietary electronic voting systems, which have 
the potential for security vulnerabilities, voting miscounts, and electoral fraud 
the elections website that the Computer Science Club uses is Open Source. This 
provides election transparency as the source code for the elections website you see 
here is the same used each year for club elections, this makes it possible for anyone
to contribute and audit the source code for security vulnerabilities or other issues
such as bugs or vote biasing.

How You Can Contribute
-----------------------

While the majority of the elections website will be complete (as it is being used
for elections each year) there are still many areas where you can contribute,
such as:

+ Auditing the source code for security vulnerabilities (important)
+ Auditing the source code for subtle changes that may be intended to rig the election
+ Adding documentation so that future club executives can easily maintain the elections
website
+ Updating the source code to support newer versions of PHP/MySQL
+ Updating the source code to fix security flaws (part of auditing)
+ Code refactoring and documentation, the project uses [phpDocumentor](http://www.phpdoc.org/)
which is similar to JavaDoc for source code documentation, but we are leaning towards 
the wiki or [Read the Docs](http://readthedocs.org/) for any other documentation

Copyright (Really Copyleft)
---------------------------

The Computer Science Club elections website uses the [GNU Affero General 
Public License, Version 3](http://www.gnu.org/licenses/agpl-3.0.html) to ensure
that the source code for the website is also provided. This prevents a loop-hole
that exists in the GPL where a website can be based off open source code but the
actual "open source" code for the website is not provided. 

Under the AGPLV3 that this software is licensed under you must ensure that the source 
code that you are using is made publicly available and accessible to everyone. In the
case of the Computer Science Club at DC and UOIT Elections Website a URL to this GitHub 
page where the source code can be obtained is provided in the footer of the website.