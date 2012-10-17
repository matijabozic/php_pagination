## About ##

Pagination is simple class that enables you to paginate arbitrary content. I built this class for my custome PHP MVC framework, but it can be used with other frameworks. I use Twig as template engine, and Doctrine ORM as relation data mapper. Framework is very similar to Symfony2, so you can easily implement this class there, or in any other framework really.

## The Goal ##

The goal of this class is not to build and return formated HTML tags that represent Pagination like other Pagination classes do. The idea is to use this class to get information about current page, pages around current page, next and back page number etc. Then you can give that information to Twig, and use Twig to add HTML tags, implement your presentation logic and build any type of pagination you want. These informations are nothing more then page numbers. I strongly belive that HTML tags belong to view layer and view layer only! And with this class we can achive that.


## Usage ##

Paginate class works with four arguments. Three are required, and last one is optional.

Required arguments:
<pre>
$page  - Current page number
$limit - How many items is shown per page
$items - Total number of items to paginate
</pre>

Optional arguments:
<pre>
$links - How many page links will be shown before and after current page, defaults to 10, but can be overriden.
</pre>

You can set those arguments through constructor like this:
<pre>
$pagination = new \Core\Pagination\Pagination($page, $limit, $items, $links);
</pre>

or you can use setter methods like this:
<pre>
$pagination = new \Core\Pagination\Pagination();
$pagination->setPage($page);
$pagination->setLimit($limit);
$pagination->setItems($items);
$pagination->setLinks(5);
</pre>

To get pagination information you use this method:
<pre>
$pagination->getPaginationInfo();
</pre>
This would return PHP array, holding information about pagination. Its joust array holding page numbers really, following array keys are available:
<pre>
pageCurrent - prints current page number
pageNext    - prints next page number
pageBack    - prints privious page number
pageFirst   - prints first page number
pageLast    - prints last page number
pagesNext   - array holding next 5 or less page numbers, defined in $links
pagesBack   - array holding previous 5 or less page numbers, defined in $links
pagesTotal  - prints total pages number
pagesLimit  - prints number of items shown per page
itemsTotal  - prints total number of items
</pre>

## Example ##

Lets say we are working on a blog, and want to show 5 blog posts per page plus pagination. The URL for this route would be this:

<pre>
/posts?page=2&limit=5
</pre>

And lets say that this URL would invoke posts method in our Blog Controller.

In our Blog Controller we would do this:
`````php
public function post($request)
{
	// Get Doctrine Entity Manager and Twig instances using DIC
	$orm = $this->container->getService("DoctrineOrm");
	$twig = $this->container->getService("Twig");
	
	// Get page variable from URL
	$page = $request->get('page');
	
	// Get limit variable from URL
	$limit = $request->get('limit');
	
	// Get total items count using Doctrine ORM
	$items = $orm->createQuery('SELECT COUNT(p.id) FROM Models\Entities\Post p')->getSingleScalarResult();
	
	// And lets set number of links before and after current page to 5
	$links = 5;
	
	// Lets also fetch posts from database, joust to make this example complete
	$offset = ($page - 1) * $limit;	
	$posts = $orm->getRepository('\Models\Entities\Post')->findBy(array(), array(), $limit, $offset);
	
	// Now lets instanciate our Pagination class with defined variables
	$pagination = new \Core\Pagination\Pagination($page, $limit, $items, $links);
	
	// And ask for pagination information
	$pinfo = $pagination->getPaginationInfo();
	
	// Finaly render the page
	$content = $twig->render('Blog/posts.html', array(
		'posts' => $posts,
		'pinfo' => $pinfo,
	));
	
	// And send HTTP Response to user
	$response = new \Core\Http\Response($content, 200);
	$response->send();
}
`````

As you can see in this example, we prepare all variables needed for fetching Posts
from database and building Pagination class, ask Paginate class to get us info 
needed to build pagination, and then pass that info to Twig template. And now its up 
to Twig to build pagination.

From Twig template we can access pinfo array that holds these values:
<pre>
{{pinfo.pageCurrent}} - prints current page number
{{pinfo.pageNext}}    - prints next page number
{{pinfo.pageBack}}    - prints privious page number
{{pinfo.pageFirst}}   - prints first page number
{{pinfo.pageLast}}    - prints last page number
{{pinfo.pagesNext}}   - array holding next 5 or less page numbers, defined in $links
{{pinfo.pagesBack}}   - array holding previous 5 or less page numbers,, defined in $links
{{pinfo.pagesTotal}}  - prints total pages number
{{pinfo.pagesLimit}}  - prints number of items shown per page
{{pinfo.itemsTotal}}  - prints total number of items
</pre>

Now lets build Twig template, Blog/posts.html can look like this:
`````twig
<div id="posts">
	{% for post in posts %}
		// -- SNIP --
		// Do your magic with posts
		// -- SNIP --
	{% endfor %}
</div>
	
<div id="pagination">
	<a href="/posts?page={{pinfo.pageFirst}}&limit={{pinfo.pagesLimit}}">First</a>
	
	{% if pinfo.pageBack is defined %}
	<a href="/posts?page={{pinfo.pageBack}}&limit={{pinfo.pagesLimit}}">Back</a>
	{% endif %}
	
	{% for page in pinfo.pagesBack %}
	<a href="/posts?page={{page}}&limit={{pinfo.pagesLimit}}">{{page}}</a>			
	{% endfor %}
	
	<b><a href="/posts?page={{pinfo.pageCurrent}}&limit={{pinfo.pagesLimit}}">{{pinfo.pageCurrent}}</a></b>
	
	{% for page in pinfo.pagesNext %}
	<a href="/posts?page={{page}}&limit={{pinfo.pagesLimit}}">{{page}}</a>			
	{% endfor %}				
	
	{% if pinfo.pageNext is defined %}
	<a href="/posts?page={{pinfo.pageNext}}&limit={{pinfo.pagesLimit}}">Next</a>
	{% endif %}				
	
	<a href="/posts?page={{pinfo.pageLast}}&limit={{pinfo.pagesLimit}}">Last</a>
	
	<p>Total items: {{pinfo.itemsTotal}}</p>
	<p>Total pages: {{pinfo.pagesTotal}}</p>
</div>
`````

This example shows you how to build default pagination, but you can use this to
build any other type of pagination you want. Just use information about pages
provided by Pagination class, and implement your presentation logic, and HTML
markup in Twig or any other template.

You could for example use this URL:
<pre>
/posts/1/5
</pre>
And then use this code in your Twig template:
`````twig
<a href="/posts/{{pinfo.pageFirst}}/{{pinfo.limit}}">First</a>
<a href="/posts/{{pinfo.pageCurrent}}/{{pinfo.limit}}">{{pinfo.pageCurrent}}</a>
<a href="/posts/{{pinfo.pageLast}}/{{pinfo.limit}}">Last</a>
`````

This example is done in my MVC framework. But you should get the point.
<code>$request->get('id');</code>
is joust a way I retrive $_GET variables through my HTTP Request object. You can 
use $_GET or Symfony HTTP Request object if you use Symfony2 or anything else.
Also, if you want to use this class in your environment, you should change namespace
and register autoloading, but if you are reading this, I know you are smart enough
to do it yourself.

## Things to note about pageNext and pageBack ##

pageNext and pageBack will be available only if there is a need for Next and
Back links. For example, if currentPage is 1, there can't be back button 
since page 0 doas not exist. The same thing is with pageNext, if you have 9 
pages in total, and pageCurrent is 9, then you cant send user to page 10 right?
Thats why Pagination object would define pageNext and pageBack only if needed. 
And in your Twig template you should check for that like this:
`````twig
{% if pagination.pageBack is defined %}
{# Back page here #}
{% endif %}
`````
or
`````twig
{% if pagination.pageNext is defined %}
{# Next page here #}
{% endif %}
`````

## Things to note about pagesNext and pagesBack ##

When using pagesBack and pagesNext be aware that these two are arrays.
They hold numbers of pages before currentPage and after currentPage.
You can set how many pages should be shown before and after current page by setting
$links variable. These arrays will hold number of links defined in $links variable, 
or less if less pages exist. For example, if you have 10 pages total, your current 
page is 7, and you set links to 5, pagesNext would hold 8, 9 and 10 since 10 is last page.
The same logic goes for pagesBack.

## Future development ##

Im pretty satisfied with how this works. Im thinking about adding pagesAll key that
would hold pagesBack + pageCurrent + pagesNext, so you could print all pages with
one for loop. But then again, you wont be able to distinct pageCurrent from other
pages. And maybe I will change names of returned array keys to something shorter. Thats it!

If you like this, find this useful, or encounter any bugs using this, contact me. 
  