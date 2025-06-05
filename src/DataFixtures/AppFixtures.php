<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Doctor;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $specialities = ["Chirurgie", "Dentiste", "Pédiatrie", "Urgence", "Gynécologie", "Généraliste"];

        for ($i = 0; $i < 30; $i++) {
            $doctor = new Doctor();
            $doctor->setFirstname($faker->firstName());
            $doctor->setLastname($faker->lastName());
            $doctor->setAddress($faker->streetAddress());
            $doctor->setImage("https://avatar.iran.liara.run/public");
            $doctor->setSpeciality($faker->randomElement($specialities));
            $doctor->setCity($faker->city());
            $doctor->setZip($faker->postcode());
            $doctor->setPhone($faker->phoneNumber());

            $manager->persist($doctor);
        }

        $admin = new User();
        $admin->setEmail('admin@admin.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword('$2y$13$YKJVWg56HofbhjpzsPPv9uY/FiEQymZRtTMUIf/fCMYkby0cgtuZ6'); // password: admin123
        $admin->setFirstname('Admin');
        $admin->setLastname('Admin');
        $manager->persist($admin);

        $manager->flush();
    }
}
