<?php

namespace App\Controllers;

require_once('../App/Models/Log.php');
//require_once('../vendor/zendframework/Zendsession/src/Container.php');

use \Core\View;
use App\Models\Log as modeldb;
use App\Models\User;


use \Paydunya\Setup;
use \Paydunya\Checkout\Store;

/*
use \Paydunya\Setup::setMasterKey("70MbRkDJ-8hey-KqNA-2Yoo-KyruRUqJ5k6a");;
use \Paydunya\Setup::setPublicKey("live_public_0yXoAbJakBRDW590EK5LJmXuIuB");
use \Paydunya\Setup::setPrivateKey("live_private_RHJ7lsEoLuf8PNja19hSekLN8ga");
use \Paydunya\Setup::setToken("NS3BTg6KApPQc5GL0kuR");
use \Paydunya\Setup::setMode("test"); // Optionnel. Utilisez cette option pour les paiements tests.

//Configuration des informations de votre service/entreprise
use \Paydunya\Checkout\Store::setName("Paiement de scolarité"); // Seul le nom est requis
use \Paydunya\Checkout\Store::setTagline("Inscription");
use \Paydunya\Checkout\Store::setPhoneNumber("+225 22 00 55 89");
use \Paydunya\Checkout\Store::setPostalAddress("Abidjan, Angré Chateau Immeuble Batim");
use \Paydunya\Checkout\Store::setWebsiteUrl("https://uges.x-pertizgroup.com/");
//use \Paydunya\Checkout\Store::setLogoUrl("https://uges.x-pertizgroup.com/logo.png");
*/

/**
 * Home controller
 *
 * PHP version 7.0
*/
class Crt_paiement extends \Core\Controller
{

    /**
     * Show the index page
     *
     * @return void
    */

    public function paiement_scolariteAction(){


        //var_dump($_POST);

        $email_eleve = htmlspecialchars($_POST['email_eleve']) ; 
        $datenais =htmlspecialchars($_POST['datenaiss_eleve']) ; 
        $infos_elev =  User::get_eleveinfos($email_eleve, $datenais);
        //var_dump($infos_elev);exit;

        //var_dump( $id_lastannee  );

        if ($infos_elev == 0) {   $page = 'Inscription.html'; }
        else {

            
                /* test
                    Clé Publique
                    test_public_P7FOVYoxWjtJcVZK9SIPRAht4lS  
                    Clé Privée
                    test_private_WE17wyWNzL5WC2LUEQLJNcpzc5Q  
                    Token
                    3OfUjOPRekyCGyPxvcyA  
                */

                /*prod
                    Clé Publique
                    live_public_PJ4MvyjanCkrs5ScjcgCLR1VEZ7  
                    Clé Privée
                    live_private_25On7D6hPjEX5Hyf21ek4QLRFBT  
                    Token
                    mROXqzqQZw8Mfokhc2Jj  
                */

                Setup::setMasterKey("93CWpnHg-8uAa-eI4L-KiM9-5GgDa066sE5Y");
                Setup::setPublicKey("test_public_P7FOVYoxWjtJcVZK9SIPRAht4lS");
                Setup::setPrivateKey("test_private_WE17wyWNzL5WC2LUEQLJNcpzc5Q");
                Setup::setToken("3OfUjOPRekyCGyPxvcyA");
                Setup::setMode("test"); // Optionnel. Utilisez cette option pour les paiements tests.

                //Configuration des informations de votre service/entreprise
                Store::setName("Paiement de scolarité (Année scolaire 2020 - 2021)"); // Seul le nom est requis
                Store::setTagline("Inscription");
                Store::setPhoneNumber("+225 22 00 55 89");
                Store::setPostalAddress("Abidjan, Angré Chateau Immeuble Batim");
                Store::setWebsiteUrl("https://uges.x-pertizgroup.com/");
                Store::setLogoUrl("https://uges.x-pertizgroup.com/public/assets/img/univ/poincare.png");
                Store::setCallbackUrl("https://uges.x-pertizgroup.com/public/setCallbackUrl");

                /*
                Dans le fichier du code source qui doit effectuer l'action procédez ainsi si vous souhaitez rediriger vos clients vers notre site Web afin qu'il puisse achever le processus de paiement:
                */

                $invoice = new \Paydunya\Checkout\CheckoutInvoice();

                $prix_scolarite = 10000;
                $libelle_paiement = "Paiement frais d'inscription ";
                $detail_paiement ="(Année scolaire 2020 - 2021)" ;
                $description_paiement =$infos_elev['nom_prenom']."  - Email : ".$infos_elev['email']."  - Contact : ".$infos_elev['contact']."  - Née le : ".$infos_elev['date_naiss']."   à : ".$infos_elev['lieu_naiss']  ;
                //A insérer dans le fichier du code source qui doit effectuer l'action

                /* L'ajout d'éléments à votre facture est très basique.
                Les paramètres attendus sont nom du produit, la quantité, le prix unitaire,
                le prix total et une description optionelle. */
                $invoice->addItem($libelle_paiement, 1, $prix_scolarite, $prix_scolarite, $detail_paiement);

                $invoice->setDescription($description_paiement);
                $invoice->setTotalAmount(10000);

                
                //A insérer dans le fichier du code source qui doit effectuer l'action// Les paramètres sont l'intitulé de la taxe et le montant de la taxe.
                //$invoice->addTax("TVA (18%)", 6300);

                
                //A insérer dans le fichier du code source qui doit effectuer l'action

                // Les données personnalisées vous permettent d'ajouter des données supplémentaires à vos informations de facture
                // que pourrez récupérer plus tard à l'aide de notre action de callback Confirm

                // Cas d'une installation via Composer (paiement annuler)
                Store::setCancelUrl("https://uges.x-pertizgroup.com/public/setCancelUrl");
                //A insérer dans le fichier du code source qui doit effectuer l'action


                // Cas d'une installation via Composer (paiement éffectué)
                Store::setReturnUrl("https://uges.x-pertizgroup.com/public/setReturnUrl");


                // Le code suivant décrit comment créer une facture de paiement au niveau de nos serveurs,
                // rediriger ensuite le client vers la page de paiement
                // et afficher ensuite son reçu de paiement en cas de succès.
                if($invoice->create()) {
                    header("Location: ".$invoice->getInvoiceUrl());
                }else{
                    echo $invoice->response_text;
                }


                $page = 'paiement_inscription_paydunya.html';


        }



        $tabledata = [];
        //var_dump($_POST,$_GET,$_FILES);//exit;

  
        
        $info = "Accès page";
        $log_user =" Page de paiement de scolarite";
        modeldb::setLog($info,$log_user);


        View::renderTemplate('Inscription/'.$page, $tabledata );

    }

    public function etatpaiementAction(){
        //A insérer dans le fichier du code source qui doit effectuer l'action

        // PayDunya rajoutera automatiquement le token de la facture sous forme de QUERYSTRING "token"
        // si vous avez configuré un "return_url" ou "cancel_url".
        // Récupérez donc le token en pur PHP via $_GET['token']
        $token = $_GET['token'];

        $invoice = new \Paydunya\Checkout\CheckoutInvoice();
        if ($invoice->confirm($token)) {

        // Récupérer le statut du paiement
        // Le statut du paiement peut être soit completed, pending, cancelled
        echo $invoice->getStatus();

        // Vous pouvez récupérer le nom, l'adresse email et le
        // numéro de téléphone du client en utilisant
        // les méthodes suivantes
        echo $invoice->getCustomerInfo('name');
        echo $invoice->getCustomerInfo('email');
        echo $invoice->getCustomerInfo('phone');

        // Les méthodes qui suivent seront disponibles si et
        // seulement si le statut du paiement est égal à "completed".

        // Récupérer l'URL du reçu PDF électronique pour téléchargement
        echo $invoice->getReceiptUrl();

        // Récupérer n'importe laquelle des données personnalisées que
        // vous avez eu à rajouter précédemment à la facture.
        // Merci de vous assurer à utiliser les mêmes clés que celles utilisées
        // lors de la configuration.
        echo $invoice->getCustomData("categorie");
        echo $invoice->getCustomData("periode");
        echo $invoice->getCustomData("numero_gagant");
        echo $invoice->getCustomData("prix");

        // Vous pouvez aussi récupérer le montant total spécifié précédemment
        echo $invoice->getTotalAmount();

        }else{
            echo $invoice->getStatus();
            echo $invoice->response_text;
            echo $invoice->response_code;
        }
    }

    

}
