# admin-crud-silex
### currently in development.

## Installation

### Using composer

Add following lines to your `composer.json` file:

### Silex 2.2
Using [fabpot/Silex-Skeleton](https://github.com/fabpot/Silex-Skeleton)

    "require": {
      ...
      "mwsimple/admin-crud-orm-silex": "2.2.*@dev"
    }

Execute:

    php composer.phar update "mwsimple/admin-crud-orm-silex"

## Dependencies

Using [dflydev/doctrine-orm-service-provider](https://github.com/dflydev/dflydev-doctrine-orm-service-provider)

## Usage

Add it to the `src/app.php` class:

	use Silex\Application;
	...
    use MWSimple\Silex\AdminCrudORMSilex\CrudController;
    ...
    //CONTROLLER POST
	$app['post.controller'] = $app->share(function() use ($app) {
	    $options = array(
	        'dirTemplate' => 'Post/',
	        'entityRepo'  => '\Post',
	        'entityName'  => 'Post',
	        'entityRoute' => 'post'
	    );
	    return new PostController($app,$options);
	});

Add it to the `src/controllers.php` class:

	//CONTROLLER POST
	$app->get('/post', "post.controller:indexAction")->bind('post');
	$app->post('/post/', "post.controller:createAction")->bind('post_create');
	$app->get('/post/new', "post.controller:newAction")->bind('post_new');
	$app->get('/post/{id}', "post.controller:showAction")->bind('post_show');
	$app->get('/post/{id}/edit', "post.controller:editAction")->bind('post_edit');
	$app->post('/post/{id}', "post.controller:updateAction")->bind('post_update')
	  ->method('PUT|POST')
	;
	$app->match('/post/{id}', "post.controller:deleteAction")->bind('post_delete')
	  ->method('DELETE')
	;

## Author

Gonzalo Alonso - gonkpo@gmail.com
