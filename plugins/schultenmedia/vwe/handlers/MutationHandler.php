<?php namespace SchultenMedia\Vwe\Handlers;

use SchultenMedia\Basics\Controllers\Robots;
use Response;
use Illuminate\Routing\Controller as ControllerBase;

/**
 * RobotsHandler
 */
class MutationHandler extends ControllerBase
{

    public function index()
    {


        $post = input();

        $obOccasionModel = \Tailor\Models\EntryRecord::inSection('Content\Occasion');
        $obOccasionModel->addJsonable(['images', 'specifications']);


        if(!$obOccasion = $obOccasionModel->where('voertuignr_hexon', $post['voertuignr_hexon'])->first()) {
            $obOccasion = (new \Tailor\Models\EntryRecord)::inSection('Content\Occasion');
            $obOccasion->voertuignr_hexon = $post['voertuignr_hexon'];
            $obOccasion->title = $post['titel'];
            $obOccasion->is_enabled = 1;
            $obOccasion->save();


        }
        $obOccasion->slug = $obOccasion->id . '-' . str_slug($obOccasion->title, '-');
        $obOccasion->save();

        if(!$obOccasion = $obOccasionModel->where('voertuignr_hexon', $post['voertuignr_hexon'])->first()) {
            die('Not found;');
        }

        switch($post['actie']) {
            case 'add':
            case 'change':

                $specifications = [];
                $obOccasion->voertuigsoort = $post['voertuigsoort'];
                $obOccasion->carrosserie = $post['carrosserie'];

                $obOccasion->merk = $post['merk'];
                $obOccasion->model = $post['model'];


                $configureerbaar = true;
                if($post['voertuigsoort'] != 'MOTOR') {
                    $configureerbaar = false;
                }
                $obOccasion->configureerbaar = $configureerbaar;


                $obOccasion->type = $post['type'];
                $obOccasion->highlights = $post['highlights'];
                $obOccasion->description = '<p>' . $post['opmerkingen'] . '</p>';
                $obOccasion->transmissie = $post['transmissie'];

                $specifications[] = [
                    'label' => 'Transmissie',
                    'value' => $post['transmissie'],
                ];

                if(!empty($post['tellerstand'])) {
                    $obOccasion->tellerstand = $post['tellerstand'];
                    $specifications[] = [
                        'label' => 'Tellerstand',
                        'value' => $post['tellerstand'],
                    ];
                }
                $specifications[] = [
                    'label' => 'Merk',
                    'value' => $post['merk'],
                ];
                if(!empty($post['kenteken'])) {
                    $obOccasion->kenteken = $post['kenteken'];
                }
                $obOccasion->brandstof = $post['brandstof'];
                $specifications[] = [
                    'label' => 'Brandstof',
                    'value' => $post['brandstof'],
                ];
                $obOccasion->bouwjaar = $post['bouwjaar'];
                $specifications[] = [
                    'label' => 'Bouwjaar',
                    'value' => $post['bouwjaar'],
                ];
                if(!empty($post['basiskleur'])) {
                    $obOccasion->basiskleur = $post['basiskleur'];
                    $specifications[] = [
                        'label' => 'Kleur',
                        'value' => $post['basiskleur'],
                    ];
                }

                $obOccasion->verkocht = $post['verkocht'] == 'n' ? 0 : 1;
                $obOccasion->nieuw = $post['nieuw'] == 'n' ? 0 : 1;

                if(!empty($post['btw_marge'])) {
                    $obOccasion->btw_marge = $post['btw_marge'];
                }
                $obOccasion->verkoopprijs = $post['verkoopprijs_particulier_bedrag'];

                if(!empty($post['vermogen_motor_pk'])) {
                    $obOccasion->vermogen_motor_pk = $post['vermogen_motor_pk'];
                }
                if(!empty($post['vermogen_motor_kw'])) {
                    $obOccasion->vermogen_motor_kw = $post['vermogen_motor_kw'];
                }
                if(!empty($post['cilinderinhoud'])) {
                    $obOccasion->cilinderinhoud = $post['cilinderinhoud'];
                    $specifications[] = [
                        'label' => 'Cilinderinhoud',
                        'value' => $post['cilinderinhoud'],
                    ];
                }
                $obOccasion->accessoires = $post['accessoires'];
                if(!empty($post['aantal_cilinders'])) {
                    $obOccasion->aantal_cilinders = $post['aantal_cilinders'];
                }
//            $specifications[] = [
//                'label' => 'Cilinders',
//                'value' => $post['aantal_cilinders'],
//            ];
                $obOccasion->is_enabled = 1;


                $obOccasion->specifications = json_encode($specifications);

                $photos = explode(',', $post['afbeeldingen']);
                if(count($obOccasion->images) != count($photos)) {
                    $obOccasion->images()->delete();
                }
                foreach($photos as $foto_nr => $foto_url) {
                    $filename = $post['voertuignr_hexon'] .'-'. $foto_nr .'.jpg';
                    $bExists = false;
                    foreach($obOccasion->images as $image) {
                        if($image->file_name == $filename) {
                            $bExists = true;
                            break;
                        }
                    }
                    if($bExists == false) {
                        $obOccasion->images()->add((new \System\Models\File)->fromUrl($foto_url, $filename));
                    }
                }


                $obOccasion->save();

                break;

            case 'delete':

                $obOccasion->is_enabled = 0;
                $obOccasion->save();

                break;


        }

        echo 1;
    }

    function controleer_voertuig() {
        if(empty($_POST['afbeeldingen'])) {
            // Foutmelding teruggeven aan server van Hexon
            print("Op de eigen website zijn alleen voertuigen met foto toegestaan");
            exit;
        }
    }

    function verwerk_fotos() {
        $fotos = explode(',', $_POST['afbeeldingen']);
        foreach($fotos as $foto_nr => $foto_url) {
            $bestandsnaam = 'fotos/'. $_POST['voertuignr_hexon'] .'-'. $foto_nr .'.jpg';

            $imgdata = file_get_contents($foto_url);
            file_put_contents($bestandsnaam, $imgdata);
        }
    }

}
