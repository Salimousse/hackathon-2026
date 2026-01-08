<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Commentaire;
use App\Models\MembreAsso;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des utilisateurs de démonstration
        $users = [
            [
                'name' => 'Jean Dupont',
                'email' => 'jean.dupont@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Marie Martin',
                'email' => 'marie.martin@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Pierre Durand',
                'email' => 'pierre.durand@example.com',
                'password' => Hash::make('password123'),
            ],
            [
                'name' => 'Sophie Bernard',
                'email' => 'sophie.bernard@example.com',
                'password' => Hash::make('password123'),
            ],
        ];

        $createdUsers = [];
        foreach ($users as $userData) {
            $createdUsers[] = User::firstOrCreate(
                ['email' => $userData['email']], 
                $userData
            );
        }

        // IDs d'associations réelles de l'API (exemples)
        $associationIds = [
            'W332017937', // PTIT-PLUS
            'W332019119', // COMITE DES VERTS COTEAUX
            'W332026296', // JET TEAM 33
            'W331000286', // ECOLE DE JUDO DE TRESSES
        ];

        // Créer des commentaires pour différentes associations
        $commentaires = [
            [
                'idUser' => $createdUsers[0]->id,
                'idAssociation' => $associationIds[0],
                'descCommentaire' => 'Excellente association ! Très accueillante et à l\'écoute des personnes âgées. Je recommande vivement.',
                'noteAssociation' => 5,
            ],
            [
                'idUser' => $createdUsers[1]->id,
                'idAssociation' => $associationIds[0],
                'descCommentaire' => 'Super initiative locale. Les bénévoles sont formidables et très dévoués.',
                'noteAssociation' => 5,
            ],
            [
                'idUser' => $createdUsers[2]->id,
                'idAssociation' => $associationIds[1],
                'descCommentaire' => 'Belle association de quartier qui défend nos intérêts. Très bon travail !',
                'noteAssociation' => 4,
            ],
            [
                'idUser' => $createdUsers[0]->id,
                'idAssociation' => $associationIds[2],
                'descCommentaire' => 'Super club de jet ski ! Ambiance conviviale et sorties régulières. On s\'amuse bien !',
                'noteAssociation' => 5,
            ],
            [
                'idUser' => $createdUsers[3]->id,
                'idAssociation' => $associationIds[2],
                'descCommentaire' => 'Bonne organisation et matériel de qualité. Parfait pour les passionnés de sports nautiques.',
                'noteAssociation' => 4,
            ],
            [
                'idUser' => $createdUsers[1]->id,
                'idAssociation' => $associationIds[3],
                'descCommentaire' => 'Excellent club de judo pour les enfants. Les professeurs sont patients et pédagogues.',
                'noteAssociation' => 5,
            ],
            [
                'idUser' => $createdUsers[2]->id,
                'idAssociation' => $associationIds[3],
                'descCommentaire' => 'Mon fils adore ses cours de judo ici. Très bonne ambiance et bon encadrement.',
                'noteAssociation' => 5,
            ],
            [
                'idUser' => $createdUsers[3]->id,
                'idAssociation' => $associationIds[1],
                'descCommentaire' => 'Association active dans le quartier. Ils organisent régulièrement des événements sympathiques.',
                'noteAssociation' => 4,
            ],
        ];

        foreach ($commentaires as $commentaireData) {
            Commentaire::firstOrCreate(
                [
                    'idUser' => $commentaireData['idUser'],
                    'idAssociation' => $commentaireData['idAssociation']
                ],
                $commentaireData
            );
        }

        // Créer des adhésions (membres d'associations)
        $memberships = [
            // Membres acceptés
            [
                'user_id' => $createdUsers[0]->id,
                'association_id' => $associationIds[0],
                'role' => 'membre',
                'status' => 'accepted',
            ],
            [
                'user_id' => $createdUsers[1]->id,
                'association_id' => $associationIds[0],
                'role' => 'membre',
                'status' => 'accepted',
            ],
            [
                'user_id' => $createdUsers[0]->id,
                'association_id' => $associationIds[2],
                'role' => 'administrateur',
                'status' => 'accepted',
            ],
            [
                'user_id' => $createdUsers[2]->id,
                'association_id' => $associationIds[3],
                'role' => 'membre',
                'status' => 'accepted',
            ],
            [
                'user_id' => $createdUsers[3]->id,
                'association_id' => $associationIds[2],
                'role' => 'membre',
                'status' => 'accepted',
            ],
            // Membres en attente
            [
                'user_id' => $createdUsers[1]->id,
                'association_id' => $associationIds[3],
                'role' => 'membre',
                'status' => 'pending',
            ],
            [
                'user_id' => $createdUsers[2]->id,
                'association_id' => $associationIds[1],
                'role' => 'membre',
                'status' => 'pending',
            ],
            [
                'user_id' => $createdUsers[3]->id,
                'association_id' => $associationIds[1],
                'role' => 'membre',
                'status' => 'accepted',
            ],
        ];

        foreach ($memberships as $membershipData) {
            MembreAsso::firstOrCreate(
                [
                    'user_id' => $membershipData['user_id'],
                    'association_id' => $membershipData['association_id']
                ],
                $membershipData
            );
        }

        $this->command->info('✅ Données de démonstration créées avec succès !');
        $this->command->info('📧 Utilisateurs créés : ' . count($createdUsers));
        $this->command->info('💬 Commentaires créés : ' . count($commentaires));
        $this->command->info('👥 Adhésions créées : ' . count($memberships));
        $this->command->line('');
        $this->command->info('🔐 Vous pouvez vous connecter avec :');
        foreach ($users as $user) {
            $this->command->line("   Email: {$user['email']} | Password: password123");
        }
    }
}
