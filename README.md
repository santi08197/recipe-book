Challenge Santiago Gonzalez

------INSTALACIÓN

-clonar el repositorio

-ejecutar $ composer install

-ejecutar $ php artisan migrate

-ejecutar $ php artisan serve

------USO DE LA API

A continuación voy explicar las diferentes rutas que posee la api y sus parametros.
Yo use Postman para probarlas e importe la collection al repo en forma de json con el nombre de recipe-book.postman_collection.json. Por cada ejemplo que mencione también voy a estar mencionando el nombre de la ruta en postman por si desean utilizarla.


CREACION DE INGREDIENTES--------------------------------------------------------------

Podemos empezar agregando ingredientes para una salsa picante.
Para ello vamos a necesitar un post en la ruta:

    POST http://127.0.0.1:8000/api/recipes

con el siguiente json en el cuerpo

    [
        {
            "name": "Chiles Habaneros",
            "unit": "g",
            "unit_price": 0.50
        },
        {
            "name": "Tomates",
            "unit": "g",
            "unit_price": 1.50
        },
        {
            "name": "Ajo",
            "unit": "g",
            "unit_price": 0.50
        },
        {
            "name": "Vinagre",
            "unit": "ml",
            "unit_price": 0.30
        },
        {
            "name": "Sal",
            "unit": "g",
            "unit_price": 0.10
        }
    ]

Cada ingrediente necesita tres parametros:
    -name: nombre del ingrediente
    -unit: unidad de medida del producto
    -unit_price: esto vendria a ser el precio de cada unidad

En el caso de los tomates cada gramo de tomate nos va a salir 1.50

nombre de la ruta en postman: Ingredients Recipe/ Ingredients Salsa Picante (POST)




CREACION DE RECETA-------------------------------------------------------------

Ahora que tenemos ingredientes creados podemos hacer con ellos una salsa picante.
Para eso vamos a necesitar saber los id de los ingredientes que necesitamos.
Esos datos los podemo usar utilizando la ruta

    GET http://127.0.0.1:8000/api/ingredients

nombre de la ruta en postman: Ingredients (GET)

Una vez obtenidos los id podemos crear la receta Salsa picante

    POST http://127.0.0.1:8000/api/recipes

    {
        "name": "Salsa Picante",
        "sale_percentage": 40,
        "ingredients": [
        {
            "ingredient_id": 158,
            "gross_amount": "200",
            "net_amount": "180"
        },
        {
            "ingredient_id": 159,
            "gross_amount": "200",
            "net_amount": "150"
        },
        {
            "ingredient_id": 160,
            "gross_amount": "50",
            "net_amount": "45"
        },
        {
            "ingredient_id": 161,
            "gross_amount": "100",
            "net_amount": "90"
        },
        {
            "ingredient_id": 162,
            "gross_amount": "10",
            "net_amount": "10"
        }
        ]
    }

Receta:
    -name: nombre de la receta
    -sale_percentage: este campo indica a cuanto vamos a vender la receta en base al precio. Por ejemplo si la receta tiene precio de 100 y de sale_porcentage tiene 50 el precio de venta va a ser de 150 (el precio y precio de venta se guardan automaticamente al crear una receta). Su valor solo puede estar entre 30 y 50 incluidos.
    -ingredients:  
        * ingredient_id: el id que buscamos previamente
        * gross_amount: la cantidad bruta que vamos a utilazar de este ingrediente en esta receta
        * gross_amount: la cantidad neta que vamos a utilazar de este ingrediente en esta receta

nombre de la ruta en postman: Recipes/ Recipe Salsa Picante (POST)




CREACION DE RECETA CON RECETA HIJA---------------------------------------------------

Ahora vamos a crear una receta de tacos de carne con la inclusion de la receta Salsa picante

Para ello vamos a agregar los ingredientes que vamos a necesitar para los tacos

    POST http://127.0.0.1:8000/api/ingredients

    [
        {
        "name": "Carne de Res",
        "unit": "g",
        "unit_price": 4
        },
        {
        "name": "Tortillas",
        "unit": "unidades",
        "unit_price": 5
        },
        {
        "name": "Cebolla",
        "unit": "unidad/es",
        "unit_price": 5
        }
    ]


Ahora nuevamente vamos a necesitar el id de los ingredientes que acabamos de crear

    GET http://127.0.0.1:8000/api/ingredients

Y tambien vamos a necesitar el id de la receta que queremos utlizar en nuestra nueva receta. En esta caso necesitamos el id de la receta Salsa picante para asociarla a la receta de Tacos de carne que vamos a crear.

    GET http://127.0.0.1:8000/api/recipes

nombre de la ruta en postman: Ingredients (GET)

Una vez obtenido el id podemos crear nuestra receta

    POST http://127.0.0.1:8000/api/recipes

    {
        "name": "Tacos de Carne",
        "sale_percentage": 50,
        "ingredients": [
            {
                "ingredient_id": 163,
                "gross_amount": 500,
                "net_amount": 400
            },
            {
                "ingredient_id": 164,
                "gross_amount": 10,
                "net_amount": 10
            },
            {
                "ingredient_id": 165,
                "gross_amount": 1,
                "net_amount": 1
            },
            {
                "childRecipeId": 77,
                "portions": 2
            }
        ]
    }

nombre de la ruta en postman: Recipes/ Recipe Tacos de Carne (POST)

Sumado a los datos mencionados previamente al crear la receta Salsa Picante tenemos dos campos nuevos en ingredients:
    -childRecipeId:id de la receta que queremos asociar a la nueva receta
    -portions: la cantidad de veces que vamos a utilizar la receta. En este caso estamos agregando dos porciones de Salsa picante a Tacos de carne.

Tanto la inclusion de una nueva receta como la cantidad de porciones afecta al precio.
Sin la salsa los tacos hubieran valido 2055, pero la inclusion de dos porciones de salsa su valor es 2967.




AGREGAR INGREDIENTE A RECETA EXISTENTE----------------------------------------------

En esta seccion vamos a crear el ingrediente pimiento y se lo vamos a asignar a la receta Tacos de carne

    POST http://127.0.0.1:8000/api/ingredients

    [
        {
            "name": "Pimiento",
            "unit": "g",
            "unit_price": 0.75
        }
    ]

nombre de la ruta en postman: Ingredients/ Ingredient pimiento (POST)


Buscamos su id para agregarlo a la receta de Tacos de carne y tambien buscamos el id de la receta a la que queremos agregar el ingrediente

    GET http://127.0.0.1:8000/api/ingredients

    GET http://127.0.0.1:8000/api/recipes

Luego relacionamos el ingrediente nuevo con la receta Tacos de carne

    POST http://127.0.0.1:8000/api/recipes/76/ingredients

    {
        "ingredient_id": 146,
        "gross_amount": 100,
        "net_amount": 90
    }

nombre de la ruta en postman: Recipe Ingredient (POST)

En la ruta va el id de la receta



ASOCIAR RECETA CON OTRA RECETA------------------------------------------------------
Esta seccion sirve para asociar recetas ya creadas.
Para la demostración de esta ruta vamos a necesitar nuestra última ruta. Vamos a crear la receta de una ensalada de pollo como lo vinimos haciendo previamente

Creamos los ingredientes

    POST http://127.0.0.1:8000/api/ingredients

    [
        {
        "name": "Lechuga",
        "unit": "g",
        "unit_price": 1.50
        },
        {
        "name": "Pollo",
        "unit": "g",
        "unit_price": 6.75
        },
        {
        "name": "Crutones",
        "unit": "g",
        "unit_price": 1.20
        },
        {
        "name": "Aceite de Oliva",
        "unit": "ml",
        "unit_price": 0.80
        }
    ]

nombre de la ruta en postman: Ingredients/ Ingredients Ensalada de Pollo (POST)

Bucamos los ids de los ingredientes

    GET http://127.0.0.1:8000/api/ingredients

Y creamos la receta

    POST http://127.0.0.1:8000/api/recipes

    {
        "name": "Ensalada de Pollo",
        "sale_percentage": 30,
        "ingredients": [
        {
            "ingredient_id": 167,
            "gross_amount": 300,
            "net_amount": 200
        },
        {
            "ingredient_id": 168,
            "gross_amount": 300,
            "net_amount": 250
        },
        {
            "ingredient_id": 169,
            "gross_amount": 100,
            "net_amount": 90
        },
        {
            "ingredient_id": 170,
            "gross_amount": 60,
            "net_amount": 40
        }
        ]
    }

nombre de la ruta en postman: Recipes/ Recipe Ensalada de Pollo (POST)

Ahora si vamos a lo que nos interesa. Si queremos, por el motivo que sea, agregarle la salsa picante a la ensalada de pollo podemos hacer lo siguiente

Primero buscamos los ids de las recetas que queremos relacionar

    GET http://127.0.0.1:8000/api/recipes

Y luego hacemos lo siguiente

    POST http://127.0.0.1:8000/api/recipes/74/child

    {
        "childRecipeId": 60,
        "portions": 1
    }

nombre de la ruta en postman: Child Recipe (POST)

En la ruta va el id de la receta padre(Ensalada de Pollo) y en el cuerpo va el nombre de la receta hija(Salsa Picante) junto con la cantidad de porciones. La inclusion de esta nueva receta afecta al precion de la receta padre.




SORT LISTADO-----------------------------------------------------------------

La ruta get recipes cuenta con la particularidad de que se le puede agregar el parametro sort para customizar el orden:

    GET http://127.0.0.1:8000/api/recipes?sort=price

Con este parametro vamos a traer todos las recetas ordenadas por precion de forma ascendente. Para que sea de forma descendente tenemos que agregar '-' al principio del parametro

    GET http://127.0.0.1:8000/api/recipes?sort=-price

Palabras validas : 'id', 'name', 'price', 'created_at', 'updated_at'




RENTABILIDAD-----------------------------------------------------------------

Si queremos saber cual es la receta mas rentable y la menos rentable podemos usar la siguiente ruta.

    GET http://127.0.0.1:8000/api/recipes/profitability

nombre de la ruta en postman: Profitability (GET)


Por rentabilidad se entiende la receta que mas ganancia tiene.

