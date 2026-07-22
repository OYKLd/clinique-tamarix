<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SpecialtySeeder extends Seeder
{
    public function run(): void
    {
        $specialties = [
            [
                'name' => 'Médecine générale',
                'icon' => 'bi-clipboard2-pulse',
                'description' => 'Consultations générales, suivi médical, bilans de santé et orientation vers les spécialistes.',
                'health_tip' => 'Un bilan de santé annuel permet de détecter tôt la plupart des maladies silencieuses comme l\'hypertension ou le diabète.',
            ],
            [
                'name' => 'Gynécologie-Obstétrique',
                'icon' => 'bi-gender-female',
                'description' => 'Suivi gynécologique, suivi de grossesse, échographies obstétricales et accouchements.',
                'health_tip' => 'Un suivi prénatal régulier dès le premier trimestre réduit significativement les risques pour la mère et l\'enfant.',
            ],
            [
                'name' => 'Pédiatrie',
                'icon' => 'bi-balloon-heart',
                'description' => 'Suivi de la croissance, vaccinations, consultations et urgences pédiatriques.',
                'health_tip' => 'Le calendrier vaccinal de votre enfant est sa meilleure protection : vérifiez qu\'il est à jour.',
            ],
            [
                'name' => 'Chirurgie générale',
                'icon' => 'bi-bandaid',
                'description' => 'Interventions chirurgicales programmées et urgentes dans un bloc opératoire moderne.',
                'health_tip' => 'Une douleur abdominale persistante ne doit jamais être négligée : consultez sans attendre.',
            ],
            [
                'name' => 'Cardiologie',
                'icon' => 'bi-heart-pulse',
                'description' => 'Consultations cardiologiques, électrocardiogrammes, échographies cardiaques et suivi de l\'hypertension.',
                'health_tip' => 'Faites contrôler votre tension artérielle au moins une fois par an, même sans symptôme.',
            ],
            [
                'name' => 'Dermatologie',
                'icon' => 'bi-sun',
                'description' => 'Diagnostic et traitement des affections de la peau, des cheveux et des ongles.',
                'health_tip' => 'Toute tache ou grain de beauté qui change de forme ou de couleur mérite un avis dermatologique.',
            ],
            [
                'name' => 'Ophtalmologie',
                'icon' => 'bi-eye',
                'description' => 'Examens de la vue, dépistage du glaucome, prescription de corrections optiques.',
                'health_tip' => 'Après 40 ans, un dépistage du glaucome tous les deux ans protège durablement votre vision.',
            ],
            [
                'name' => 'ORL',
                'icon' => 'bi-ear',
                'description' => 'Prise en charge des affections du nez, de la gorge et des oreilles, chez l\'adulte et l\'enfant.',
                'health_tip' => 'Des angines à répétition chez l\'enfant justifient une consultation ORL spécialisée.',
            ],
            [
                'name' => 'Traumatologie-Orthopédie',
                'icon' => 'bi-universal-access',
                'description' => 'Traitement des fractures, entorses et pathologies des os et des articulations.',
                'health_tip' => 'Après un traumatisme, une immobilisation adaptée dans les premières heures conditionne la bonne récupération.',
            ],
            [
                'name' => 'Imagerie médicale',
                'icon' => 'bi-camera2',
                'description' => 'Radiographie, échographie et examens d\'imagerie sur un plateau technique de dernière génération.',
                'health_tip' => 'Apportez toujours vos anciens examens d\'imagerie : la comparaison affine le diagnostic.',
            ],
        ];

        foreach ($specialties as $index => $data) {
            Specialty::firstOrCreate(
                ['slug' => Str::slug($data['name'])],
                [...$data, 'sort_order' => $index + 1],
            );
        }
    }
}
