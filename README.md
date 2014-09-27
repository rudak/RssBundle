RssBundle
=========

Bundle qui crée votre flux RSS à la volée, (après chaque création de contenu).

##Installation
* composer.json => require ```"rudak/rss-bundle": "dev-master"```
* app_kernel => ```new Rudak\RssBundle\RudakRssBundle()```

##Utilisation

1. Il faut chopper le service nommé ```rss.generator``` et en faire une variable par exemple $RssGen.
2. Copier le fichier ```resources/config/services.yml.dist``` vers ```resources/config/services.yml``` et éditez ce dernier. c'est assez intuitif si vous connaissez déja ce qui vient se coller dans le rss final, sinon la doc sur le protocole rss aide bien... Si vous ne savez pas, ne modifiez pas...
3. Il faut bien configurer le fichier service.xml, surtout les associations, et les parametres de routing items.route et items.params
4. Il faut que le générateur sache sur quelles entités il doit boucler pour creer le flux, donc soit on lui passe le tableau d'entités (les 15 dernieres par exemple, ou toute la liste mais ca peut etre lourd...) grace a ```setRssEntities()``` , soit on renseigne les parametres ```entity.repository``` et ```entity.methodName``` dans service.xml et on laisse le générateur attraper lui meme les entités.
5. Une fois que tout est pret, il faut appeler ```makeTheRssContent()``` (methode de $RssGen) pour fabriquer le contenu et hydrater les attributs du générateur.
6. A la suite, lancer ```writeTheRssFile()``` pour balancer ces données dans le fichier ```rss.xml```. il sera écrit dans le dossier web/ vérifiez que vous avez la permission d'y écrire. (pas encore modifiable dans le fichier de config)

voila, dans un premier temps ca doit le faire...

# Attention
Ce bundle est en béta donc utiliser avec douceur...
