#DT Plugin


*Date/Time plugin for ExpressionEngine*

The DT Plugin displays dates with formatting and allows you to add days, months, years to the current date or a static date you set.

***

## Change Log

- Version 1.4.3  
Added check for // in set method ( Feature ticket <a href="http://devot-ee.com/add-ons/support/dt-plugin/viewthread/13740"> http://devot-ee.com/add-ons/support/dt-plugin/viewthread/13740</a> )

- Version 1.4.2  
Fixed bug in 2.8.0 ( Thanks <a href="http://devot-ee.com/profile/user90086456"> @springworks</a> )

- Version 1.4.1  
Fixed Count and total_results  bug by using correct template parser  (Thanks <a href="http://devot-ee.com/members/profile/bransinanderson">@bransin</a>)

- Version 1.4  
Added dt_no_results to fix no_results bug when wrapping entry loops

- Version 1.3  
Added wrap tag for using with entry loops

- Version 1.1  
Fixed parse bug (thanks Ben)
 
- Version 1  
Added hour, minute, second to plugin
EE format is now default - use ee_format="false" for php format

- Version 0.2  
Update to include EE date formats by using ee_format="true"
Also fixed constructor

- Version 0.1  
Initial release

***

## DT v1.0
The DT Plugin displays dates with formatting and allows you to add days, months, years to the current date or a static date you set.

***

## The Tag


    {exp:dt}

Will return the date formatted this way: **MM/DD/YYYY**

    {exp:dt:wrap}

Tag pair used to wrap around entries loop to adding custom dates in to parameters

***

#### TAG PARAMETERS


**set=[optional]**  
Sets the static date that the other parameters will reference. If not set will default to the current date.

{exp:dt set="01/01/2010"}

  // 01/01/2010

***

**format=[optional]**  

Sets the format of the returned date using the  following parameters: http://expressionengine.com/user_guide/templates/date_variable_formatting.html
   
    {exp:dt set="11/03/90" ee_format="true" format="%l %M %j, %Y"}

Outputs: **Saturday Nov 3, 1990**

***
  
**ee_format=[optional]**  

if set to 'false'dt will parse the date using php formating: http://php.net/manual/en/function.strftime.php 
   
    {exp:dt set="11/03/90" format="%A %b %e, %Y"}

Outputs: **Saturday Nov 3, 1990**
    

***

**day=[optional]**  
  Adds or subtracts a value in days to current/set date.

**month=[optional]**  
  Adds or subtracts a value in months to current/set date.

**year=[optional]**  
  Adds or subtracts a value in years to current/set date.

    {exp:dt day="3" month="1" year="-1" set="1/1/2010"}

Outputs: **02/04/09**


***

**hour=[optional]**  
  Adds or subtracts a value in hours to current/set date.

**minute=[optional]**  
  Adds or subtracts a value in minutes to current/set date.

**second=[optional]**  
  Adds or subtracts a value in seconds to current/set date.

***

## TROUBLESHOOTING:


Please email any bugs or questions to dev@viminteractive.com