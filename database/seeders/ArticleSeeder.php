<?php

namespace Database\Seeders;

use App\Enums\ArticleCategory;
use App\Enums\UserRole;
use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::where('role', UserRole::Administration)->first();

        $articles = [
            [
                'title' => 'Hypertension : le dépistage précoce peut vous sauver la vie',
                'category' => ArticleCategory::ConseilSante,
                'excerpt' => 'L\'hypertension artérielle touche près d\'un adulte sur trois en Afrique de l\'Ouest, souvent sans aucun symptôme.',
                'content' => "<p>L'hypertension artérielle est souvent appelée « le tueur silencieux » : elle ne provoque généralement aucun symptôme pendant des années, tout en abîmant progressivement le cœur, les reins et les vaisseaux sanguins.</p><h2>Pourquoi se faire dépister ?</h2><p>En Côte d'Ivoire comme dans toute l'Afrique de l'Ouest, près d'un adulte sur trois est concerné. Un simple contrôle de la tension, indolore et rapide, permet de détecter le problème avant les complications : AVC, infarctus, insuffisance rénale.</p><h2>Les bons réflexes</h2><ul><li>Faites contrôler votre tension au moins une fois par an après 30 ans.</li><li>Réduisez votre consommation de sel et d'aliments transformés.</li><li>Pratiquez une activité physique régulière : 30 minutes de marche par jour suffisent.</li><li>En cas d'antécédents familiaux, parlez-en à votre médecin.</li></ul><p>Notre service de cardiologie vous accueille pour un bilan complet, sur simple rendez-vous en ligne.</p>",
            ],
            [
                'title' => 'Vaccination des enfants : le calendrier à connaître',
                'category' => ArticleCategory::ConseilSante,
                'excerpt' => 'De la naissance à l\'adolescence, chaque vaccin compte. Le point avec notre service de pédiatrie.',
                'content' => "<p>La vaccination reste l'un des gestes de prévention les plus efficaces pour protéger votre enfant contre des maladies graves : rougeole, poliomyélite, fièvre jaune, hépatite B…</p><h2>Les étapes clés</h2><ul><li><strong>À la naissance :</strong> BCG et première dose d'hépatite B.</li><li><strong>6, 10 et 14 semaines :</strong> pentavalent, polio, pneumocoque, rotavirus.</li><li><strong>9 mois :</strong> rougeole et fièvre jaune.</li><li><strong>15-18 mois :</strong> rappels essentiels.</li></ul><h2>Un doute sur le carnet de votre enfant ?</h2><p>Notre équipe de pédiatrie vérifie gratuitement le carnet de vaccination lors de chaque consultation et met à jour les vaccins manquants. Prenez rendez-vous en quelques clics.</p>",
            ],
            [
                'title' => 'Suivi de grossesse : les consultations à ne pas manquer',
                'category' => ArticleCategory::ConseilSante,
                'excerpt' => 'Un suivi prénatal régulier est la meilleure garantie d\'une grossesse sereine et d\'un bébé en bonne santé.',
                'content' => "<p>Dès les premières semaines de grossesse, un suivi médical régulier permet de veiller sur votre santé et celle de votre bébé.</p><h2>Le rythme recommandé</h2><ul><li>Une consultation par mois jusqu'au 7e mois.</li><li>Deux consultations au 8e mois, puis un suivi rapproché jusqu'à l'accouchement.</li><li>Trois échographies de référence : 12 SA, 22 SA et 32 SA.</li></ul><h2>À la Clinique Tamarix</h2><p>Notre service de gynécologie-obstétrique vous accompagne de la confirmation de la grossesse jusqu'à l'accouchement, avec un plateau technique moderne et une équipe disponible 24h/24 pour les urgences obstétricales.</p>",
            ],
            [
                'title' => 'La Clinique Tamarix ouvre ses portes à Abidjan',
                'category' => ArticleCategory::Actualite,
                'excerpt' => 'Une nouvelle structure médico-chirurgicale moderne au service des familles, sous le signe de l\'espérance.',
                'content' => "<p>C'est avec une grande fierté que la Clinique Médico-Chirurgicale Tamarix annonce l'ouverture de ses portes à Abidjan.</p><p>Fidèle à sa devise « Nous plantons l'Espérance », la clinique met à la disposition des patients :</p><ul><li>Une équipe médicale pluridisciplinaire expérimentée ;</li><li>Un plateau technique moderne : imagerie, laboratoire, bloc opératoire ;</li><li>Un service d'urgences disponible 24h/24 et 7j/7 ;</li><li>La prise de rendez-vous en ligne, simple et rapide, avec confirmation WhatsApp.</li></ul><p>Toute l'équipe vous souhaite la bienvenue.</p>",
            ],
        ];

        foreach ($articles as $index => $data) {
            Article::firstOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    ...$data,
                    'user_id' => $author?->id,
                    'is_published' => true,
                    'published_at' => now()->subDays(count($articles) - $index),
                ],
            );
        }
    }
}
