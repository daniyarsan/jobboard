<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Profile;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->name = array(
            'Allison',
            'Arthur',
            'Ana',
            'Alex',
            'Arlene',
            'Alberto',
            'Barry',
            'Bertha',
            'Bill',
            'Bonnie',
            'Bret',
            'Beryl',
            'Chantal',
            'Cristobal',
            'Claudette',
            'Charley',
            'Cindy',
            'Chris',
            'Dean',
            'Dolly',
            'Danny',
            'Danielle',
            'Dennis',
            'Debby',
            'Erin',
            'Edouard',
            'Erika',
            'Earl',
            'Emily',
            'Ernesto',
            'Felix',
            'Fay',
            'Fabian',
            'Frances',
            'Franklin',
            'Florence',
            'Gabielle',
            'Gustav',
            'Grace',
            'Gaston',
            'Gert',
            'Gordon',
            'Humberto',
            'Hanna',
            'Henri',
            'Hermine',
            'Harvey',
            'Helene',
            'Iris',
            'Isidore',
            'Isabel',
            'Ivan',
            'Irene',
            'Isaac',
            'Jerry',
            'Josephine',
            'Juan',
            'Jeanne',
            'Jose',
            'Joyce',
            'Karen',
            'Kyle',
            'Kate',
            'Karl',
            'Katrina',
            'Kirk',
            'Lorenzo',
            'Lili',
            'Larry',
            'Lisa',
            'Lee',
            'Leslie',
            'Michelle',
            'Marco',
            'Mindy',
            'Maria',
            'Michael',
            'Noel',
            'Nana',
            'Nicholas',
            'Nicole',
            'Nate',
            'Nadine',
            'Olga',
            'Omar',
            'Odette',
            'Otto',
            'Ophelia',
            'Oscar',
            'Pablo',
            'Paloma',
            'Peter',
            'Paula',
            'Philippe',
            'Patty',
            'Rebekah',
            'Rene',
            'Rose',
            'Richard',
            'Rita',
            'Rafael',
            'Sebastien',
            'Sally',
            'Sam',
            'Shary',
            'Stan',
            'Sandy',
            'Tanya',
            'Teddy',
            'Teresa',
            'Tomas',
            'Tammy',
            'Tony',
            'Van',
            'Vicky',
            'Victor',
            'Virginie',
            'Vince',
            'Valerie',
            'Wendy',
            'Wilfred',
            'Wanda',
            'Walter',
            'Wilma',
            'William',
            'Kumiko',
            'Aki',
            'Miharu',
            'Chiaki',
            'Michiyo',
            'Itoe',
            'Nanaho',
            'Reina',
            'Emi',
            'Yumi',
            'Ayumi',
            'Kaori',
            'Sayuri',
            'Rie',
            'Miyuki',
            'Hitomi',
            'Naoko',
            'Miwa',
            'Etsuko',
            'Akane',
            'Kazuko',
            'Miyako',
            'Youko',
            'Sachiko',
            'Mieko',
            'Toshie',
            'Junko');
        $this->lastName = array(
            'Abbott',
            'Acevedo',
            'Acosta',
            'Adams',
            'Adkins',
            'Aguilar',
            'Aguirre',
            'Albert',
            'Alexander',
            'Alford',
            'Allen',
            'Allison',
            'Alston',
            'Alvarado',
            'Alvarez',
            'Anderson',
            'Andrews',
            'Anthony',
            'Armstrong',
            'Arnold',
            'Ashley',
            'Atkins',
            'Atkinson',
            'Austin',
            'Avery',
            'Avila',
            'Ayala',
            'Ayers',
            'Bailey',
            'Baird',
            'Baker',
            'Baldwin',
            'Ball',
            'Ballard',
            'Banks',
            'Barber',
            'Barker',
            'Barlow',
            'Barnes',
            'Barnett',
            'Barr',
            'Barrera',
            'Barrett',
            'Barron',
            'Barry',
            'Bartlett',
            'Barton',
            'Bass',
            'Bates',
            'Battle',
            'Bauer',
            'Baxter',
            'Beach',
            'Bean',
            'Beard',
            'Beasley',
            'Beck',
            'Becker',
            'Bell',
            'Bender',
            'Benjamin',
            'Bennett',
            'Benson',
            'Bentley',
            'Benton',
            'Berg',
            'Berger',
            'Bernard',
            'Berry',
            'Best',
            'Bird',
            'Bishop',
            'Black',
            'Blackburn',
            'Blackwell',
            'Blair',
            'Blake',
            'Blanchard',
            'Blankenship',
            'Blevins',
            'Bolton',
            'Bond',
            'Bonner',
            'Booker',
            'Boone',
            'Booth',
            'Bowen',
            'Bowers',
            'Bowman',
            'Boyd',
            'Boyer',
            'Boyle',
            'Bradford',
            'Bradley',
            'Bradshaw',
            'Brady',
            'Branch',
            'Bray',
            'Brennan',
            'Brewer',
            'Bridges',
            'Briggs',
            'Bright',
            'Britt',
            'Brock',
            'Brooks',
            'Brown',
            'Browning',
            'Bruce',
            'Bryan',
            'Bryant',
            'Buchanan',
            'Buck',
            'Buckley',
            'Buckner',
            'Bullock',
            'Burch',
            'Burgess',
            'Burke',
            'Burks',
            'Burnett',
            'Burns',
            'Burris',
            'Burt',
            'Burton',
            'Bush',
            'Butler',
            'Byers',
            'Byrd',
            'Cabrera',
            'Cain',
            'Calderon',
            'Caldwell',
            'Calhoun',
            'Callahan',
            'Camacho',
            'Cameron',
            'Campbell',
            'Campos',
            'Cannon',
            'Cantrell',
            'Cantu',
            'Cardenas',
            'Carey',
            'Carlson',
            'Carney',
            'Carpenter',
            'Carr',
            'Carrillo',
            'Carroll',
            'Carson',
            'Carter',
            'Carver',
            'Case',
            'Casey',
            'Cash',
            'Castaneda',
            'Castillo',
            'Castro',
            'Cervantes',
            'Chambers',
            'Chan',
            'Chandler',
            'Chaney',
            'Chang',
            'Chapman',
            'Charles',
            'Chase',
            'Chavez',
            'Chen',
            'Cherry',
            'Christensen',
            'Christian',
            'Church',
            'Clark',
            'Clarke',
            'Clay',
            'Clayton',
            'Clements',
            'Clemons',
            'Cleveland',
            'Cline',
            'Cobb',
            'Cochran',
            'Coffey',
            'Cohen',
            'Cole',
            'Coleman',
            'Collier',
            'Collins',
            'Colon',
            'Combs',
            'Compton',
            'Conley',
            'Conner',
            'Conrad',
            'Contreras',
            'Conway',
            'Cook',
            'Cooke',
            'Cooley',
            'Cooper',
            'Copeland',
            'Cortez',
            'Cote',
            'Cotton',
            'Cox',
            'Craft',
            'Craig',
            'Crane',
            'Crawford',
            'Crosby',
            'Cross',
            'Cruz',
            'Cummings',
            'Cunningham',
            'Curry',
            'Curtis',
            'Dale',
            'Dalton',
            'Daniel',
            'Daniels',
            'Daugherty',
            'Davenport',
            'David',
            'Davidson',
            'Davis',
            'Dawson',
            'Day',
            'Dean',
            'Decker',
            'Dejesus',
            'Delacruz',
            'Delaney',
            'Deleon',
            'Delgado',
            'Dennis',
            'Diaz',
            'Dickerson',
            'Dickson',
            'Dillard',
            'Dillon',
            'Dixon',
            'Dodson',
            'Dominguez',
            'Donaldson',
            'Donovan',
            'Dorsey',
            'Dotson',
            'Douglas',
            'Downs',
            'Doyle',
            'Drake',
            'Dudley',
            'Duffy',
            'Duke',
            'Duncan',
            'Dunlap',
            'Dunn',
            'Duran',
            'Durham',
            'Dyer',
            'Eaton',
            'Edwards',
            'Elliott',
            'Ellis',
            'Ellison',
            'Emerson',
            'England',
            'English',
            'Erickson',
            'Espinoza',
            'Estes',
            'Estrada',
            'Evans',
            'Everett',
            'Ewing',
            'Farley',
            'Farmer',
            'Farrell',
            'Faulkner',
            'Ferguson',
            'Fernandez',
            'Ferrell',
            'Fields',
            'Figueroa',
            'Finch',
            'Finley',
            'Fischer',
            'Fisher',
            'Fitzgerald',
            'Fitzpatrick',
            'Fleming',
            'Fletcher',
            'Flores',
            'Flowers',
            'Floyd',
            'Flynn',
            'Foley',
            'Forbes',
            'Ford',
            'Foreman',
            'Foster',
            'Fowler',
            'Fox',
            'Francis',
            'Franco',
            'Frank',
            'Franklin',
            'Franks',
            'Frazier',
            'Frederick',
            'Freeman',
            'French',
            'Frost',
            'Fry',
            'Frye',
            'Fuentes',
            'Fuller',
            'Fulton',
            'Gaines',
            'Gallagher',
            'Gallegos',
            'Galloway',
            'Gamble',
            'Garcia',
            'Gardner',
            'Garner',
            'Garrett',
            'Garrison',
            'Garza',
            'Gates',
            'Gay',
            'Gentry',
            'George',
            'Gibbs',
            'Gibson',
            'Gilbert',
            'Giles',
            'Gill',
            'Gillespie',
            'Gilliam',
            'Gilmore',
            'Glass',
            'Glenn',
            'Glover',
            'Goff',
            'Golden',
            'Gomez',
            'Gonzales',
            'Gonzalez',
            'Good',
            'Goodman',
            'Goodwin',
            'Gordon',
            'Gould',
            'Graham',
            'Grant',
            'Graves',
            'Gray',
            'Green',
            'Greene',
            'Greer',
            'Gregory',
            'Griffin',
            'Griffith',
            'Grimes',
            'Gross',
            'Guerra',
            'Guerrero',
            'Guthrie',
            'Gutierrez',
            'Guy',
            'Guzman',
            'Hahn',
            'Hale',
            'Haley',
            'Hall',
            'Hamilton',
            'Hammond',
            'Hampton',
            'Hancock',
            'Haney',
            'Hansen',
            'Hanson',
            'Hardin',
            'Harding',
            'Hardy',
            'Harmon',
            'Harper',
            'Harrell',
            'Harrington',
            'Harris',
            'Harrison',
            'Hart',
            'Hartman',
            'Harvey',
            'Hatfield',
            'Hawkins',
            'Hayden',
            'Hayes',
            'Haynes',
            'Hays',
            'Head',
            'Heath',
            'Hebert',
            'Henderson',
            'Hendricks',
            'Hendrix',
            'Henry',
            'Hensley',
            'Henson',
            'Herman',
            'Hernandez',
            'Herrera',
            'Herring',
            'Hess',
            'Hester',
            'Hewitt',
            'Hickman',
            'Hicks',
            'Higgins',
            'Hill',
            'Hines',
            'Hinton',
            'Hobbs',
            'Hodge',
            'Hodges',
            'Hoffman',
            'Hogan',
            'Holcomb',
            'Holden',
            'Holder',
            'Holland',
            'Holloway',
            'Holman',
            'Holmes',
            'Holt',
            'Hood',
            'Hooper',
            'Hoover',
            'Hopkins',
            'Hopper',
            'Horn',
            'Horne',
            'Horton',
            'House',
            'Houston',
            'Howard',
            'Howe',
            'Howell',
            'Hubbard',
            'Huber',
            'Hudson',
            'Huff',
            'Huffman',
            'Hughes',
            'Hull',
            'Humphrey',
            'Hunt',
            'Hunter',
            'Hurley',
            'Hurst',
            'Hutchinson',
            'Hyde',
            'Ingram',
            'Irwin',
            'Jackson',
            'Jacobs',
            'Jacobson',
            'James',
            'Jarvis',
            'Jefferson',
            'Jenkins',
            'Jennings',
            'Jensen',
            'Jimenez',
            'Johns',
            'Johnson',
            'Johnston',
            'Jones',
            'Jordan',
            'Joseph',
            'Joyce',
            'Joyner',
            'Juarez',
            'Justice',
            'Kane',
            'Kaufman',
            'Keith',
            'Keller',
            'Kelley',
            'Kelly',
            'Kemp',
            'Kennedy',
            'Kent',
            'Kerr',
            'Key',
            'Kidd',
            'Kim',
            'King',
            'Kinney',
            'Kirby',
            'Kirk',
            'Kirkland',
            'Klein',
            'Kline',
            'Knapp',
            'Knight',
            'Knowles',
            'Knox',
            'Koch',
            'Kramer',
            'Lamb',
            'Lambert',
            'Lancaster',
            'Landry',
            'Lane',
            'Lang',
            'Langley',
            'Lara',
            'Larsen',
            'Larson',
            'Lawrence',
            'Lawson',
            'Le',
            'Leach',
            'Leblanc',
            'Lee',
            'Leon',
            'Leonard',
            'Lester',
            'Levine',
            'Levy',
            'Lewis',
            'Lindsay',
            'Lindsey',
            'Little',
            'Livingston',
            'Lloyd',
            'Logan',
            'Long',
            'Lopez',
            'Lott',
            'Love',
            'Lowe',
            'Lowery',
            'Lucas',
            'Luna',
            'Lynch',
            'Lynn',
            'Lyons',
            'Macdonald',
            'Macias',
            'Mack',
            'Madden',
            'Maddox',
            'Maldonado',
            'Malone',
            'Mann',
            'Manning',
            'Marks',
            'Marquez',
            'Marsh',
            'Marshall',
            'Martin',
            'Martinez',
            'Mason',
            'Massey',
            'Mathews',
            'Mathis',
            'Matthews',
            'Maxwell',
            'May',
            'Mayer',
            'Maynard',
            'Mayo',
            'Mays',
            'Mcbride',
            'Mccall',
            'Mccarthy',
            'Mccarty',
            'Mcclain',
            'Mcclure',
            'Mcconnell',
            'Mccormick',
            'Mccoy',
            'Mccray',
            'Mccullough',
            'Mcdaniel',
            'Mcdonald',
            'Mcdowell',
            'Mcfadden',
            'Mcfarland',
            'Mcgee',
            'Mcgowan',
            'Mcguire',
            'Mcintosh',
            'Mcintyre',
            'Mckay',
            'Mckee',
            'Mckenzie',
            'Mckinney',
            'Mcknight',
            'Mclaughlin',
            'Mclean',
            'Mcleod',
            'Mcmahon',
            'Mcmillan',
            'Mcneil',
            'Mcpherson',
            'Meadows',
            'Medina',
            'Mejia',
            'Melendez',
            'Melton',
            'Mendez',
            'Mendoza',
            'Mercado',
            'Mercer',
            'Merrill',
            'Merritt',
            'Meyer',
            'Meyers',
            'Michael',
            'Middleton',
            'Miles',
            'Miller',
            'Mills',
            'Miranda',
            'Mitchell',
            'Molina',
            'Monroe',
            'Montgomery',
            'Montoya',
            'Moody',
            'Moon',
            'Mooney',
            'Moore',
            'Morales',
            'Moran',
            'Moreno',
            'Morgan',
            'Morin',
            'Morris',
            'Morrison',
            'Morrow',
            'Morse',
            'Morton',
            'Moses',
            'Mosley',
            'Moss',
            'Mueller',
            'Mullen',
            'Mullins',
            'Munoz',
            'Murphy',
            'Murray',
            'Myers',
            'Nash',
            'Navarro',
            'Neal',
            'Nelson',
            'Newman',
            'Newton',
            'Nguyen',
            'Nichols',
            'Nicholson',
            'Nielsen',
            'Nieves',
            'Nixon',
            'Noble',
            'Noel',
            'Nolan',
            'Norman',
            'Norris',
            'Norton',
            'Nunez',
            'Obrien',
            'Ochoa',
            'Oconnor',
            'Odom',
            'Odonnell',
            'Oliver',
            'Olsen',
            'Olson',
            'Oneal',
            'Oneil',
            'Oneill',
            'Orr',
            'Ortega',
            'Ortiz',
            'Osborn',
            'Osborne',
            'Owen',
            'Owens',
            'Pace',
            'Pacheco',
            'Padilla',
            'Page',
            'Palmer',
            'Park',
            'Parker',
            'Parks',
            'Parrish',
            'Parsons',
            'Pate',
            'Patel',
            'Patrick',
            'Patterson',
            'Patton',
            'Paul',
            'Payne',
            'Pearson',
            'Peck',
            'Pena',
            'Pennington',
            'Perez',
            'Perkins',
            'Perry',
            'Peters',
            'Petersen',
            'Peterson',
            'Petty',
            'Phelps',
            'Phillips',
            'Pickett',
            'Pierce',
            'Pittman',
            'Pitts',
            'Pollard',
            'Poole',
            'Pope',
            'Porter',
            'Potter',
            'Potts',
            'Powell',
            'Powers',
            'Pratt',
            'Preston',
            'Price',
            'Prince',
            'Pruitt',
            'Puckett',
            'Pugh',
            'Quinn',
            'Ramirez',
            'Ramos',
            'Ramsey',
            'Randall',
            'Randolph',
            'Rasmussen',
            'Ratliff',
            'Ray',
            'Raymond',
            'Reed',
            'Reese',
            'Reeves',
            'Reid',
            'Reilly',
            'Reyes',
            'Reynolds',
            'Rhodes',
            'Rice',
            'Rich',
            'Richard',
            'Richards',
            'Richardson',
            'Richmond',
            'Riddle',
            'Riggs',
            'Riley',
            'Rios',
            'Rivas',
            'Rivera',
            'Rivers',
            'Roach',
            'Robbins',
            'Roberson',
            'Roberts',
            'Robertson',
            'Robinson',
            'Robles',
            'Rocha',
            'Rodgers',
            'Rodriguez',
            'Rodriquez',
            'Rogers',
            'Rojas',
            'Rollins',
            'Roman',
            'Romero',
            'Rosa',
            'Rosales',
            'Rosario',
            'Rose',
            'Ross',
            'Roth',
            'Rowe',
            'Rowland',
            'Roy',
            'Ruiz',
            'Rush',
            'Russell',
            'Russo',
            'Rutledge',
            'Ryan',
            'Salas',
            'Salazar',
            'Salinas',
            'Sampson',
            'Sanchez',
            'Sanders',
            'Sandoval',
            'Sanford',
            'Santana',
            'Santiago',
            'Santos',
            'Sargent',
            'Saunders',
            'Savage',
            'Sawyer',
            'Schmidt',
            'Schneider',
            'Schroeder',
            'Schultz',
            'Schwartz',
            'Scott',
            'Sears',
            'Sellers',
            'Serrano',
            'Sexton',
            'Shaffer',
            'Shannon',
            'Sharp',
            'Sharpe',
            'Shaw',
            'Shelton',
            'Shepard',
            'Shepherd',
            'Sheppard',
            'Sherman',
            'Shields',
            'Short',
            'Silva',
            'Simmons',
            'Simon',
            'Simpson',
            'Sims',
            'Singleton',
            'Skinner',
            'Slater',
            'Sloan',
            'Small',
            'Smith',
            'Snider',
            'Snow',
            'Snyder',
            'Solis',
            'Solomon',
            'Sosa',
            'Soto',
            'Sparks',
            'Spears',
            'Spence',
            'Spencer',
            'Stafford',
            'Stanley',
            'Stanton',
            'Stark',
            'Steele',
            'Stein',
            'Stephens',
            'Stephenson',
            'Stevens',
            'Stevenson',
            'Stewart',
            'Stokes',
            'Stone',
            'Stout',
            'Strickland',
            'Strong',
            'Stuart',
            'Suarez',
            'Sullivan',
            'Summers',
            'Sutton',
            'Swanson',
            'Sweeney',
            'Sweet',
            'Sykes',
            'Talley',
            'Tanner',
            'Tate',
            'Taylor',
            'Terrell',
            'Terry',
            'Thomas',
            'Thompson',
            'Thornton',
            'Tillman',
            'Todd',
            'Torres',
            'Townsend',
            'Tran',
            'Travis',
            'Trevino',
            'Trujillo',
            'Tucker',
            'Turner',
            'Tyler',
            'Tyson',
            'Underwood',
            'Valdez',
            'Valencia',
            'Valentine',
            'Valenzuela',
            'Vance',
            'Vang',
            'Vargas',
            'Vasquez',
            'Vaughan',
            'Vaughn',
            'Vazquez',
            'Vega',
            'Velasquez',
            'Velazquez',
            'Velez',
            'Villarreal',
            'Vincent',
            'Vinson',
            'Wade',
            'Wagner',
            'Walker',
            'Wall',
            'Wallace',
            'Waller',
            'Walls',
            'Walsh',
            'Walter',
            'Walters',
            'Walton',
            'Ward',
            'Ware',
            'Warner',
            'Warren',
            'Washington',
            'Waters',
            'Watkins',
            'Watson',
            'Watts',
            'Weaver',
            'Webb',
            'Weber',
            'Webster',
            'Weeks',
            'Weiss',
            'Welch',
            'Wells',
            'West',
            'Wheeler',
            'Whitaker',
            'White',
            'Whitehead',
            'Whitfield',
            'Whitley',
            'Whitney',
            'Wiggins',
            'Wilcox',
            'Wilder',
            'Wiley',
            'Wilkerson',
            'Wilkins',
            'Wilkinson',
            'William',
            'Williams',
            'Williamson',
            'Willis',
            'Wilson',
            'Winters',
            'Wise',
            'Witt',
            'Wolf',
            'Wolfe',
            'Wong',
            'Wood',
            'Woodard',
            'Woods',
            'Woodward',
            'Wooten',
            'Workman',
            'Wright',
            'Wyatt',
            'Wynn',
            'Yang',
            'Yates',
            'York',
            'Young',
            'Zamora',
            'Zimmerman');
        $this->countries = array(
                "AF",
                "AL",
                "DZ",
                "AS",
                "AD",
                "AO",
                "AI",
                "AQ",
                "AG",
                "AR",
                "AM",
                "AW",
                "AU",
                "AT",
                "AZ",
                "BS",
                "BH",
                "BD",
                "BB",
                "BY",
                "BE",
                "BZ",
                "BJ",
                "BM",
                "BT",
                "BO",
                "BA",
                "BW",
                "BV",
                "BR",
                "IO",
                "BN",
                "BG",
                "BF",
                "BI",
                "KH",
                "CM",
                "CA",
                "CV",
                "KY",
                "CF",
                "TD",
                "CL",
                "CN",
                "CX",
                "CC",
                "CO",
                "KM",
                "CG",
                "CD",
                "CK",
                "CR",
                "CI",
                "HR",
                "CU",
                "CY",
                "CZ",
                "DK",
                "DJ",
                "DM",
                "DO",
                "EC",
                "EG",
                "SV",
                "GQ",
                "ER",
                "EE",
                "ET",
                "FK",
                "FO",
                "FJ",
                "FI",
                "FR",
                "GF",
                "PF",
                "TF",
                "GA",
                "GM",
                "GE",
                "DE",
                "GH",
                "GI",
                "GR",
                "GL",
                "GD",
                "GP",
                "GU",
                "GT",
                "GN",
                "GW",
                "GY",
                "HT",
                "HM",
                "VA",
                "HN",
                "HK",
                "HU",
                "IS",
                "IN",
                "ID",
                "IR",
                "IQ",
                "IE",
                "IL",
                "IT",
                "JM",
                "JP",
                "JO",
                "KZ",
                "KE",
                "KI",
                "KP",
                "KR",
                "KW",
                "KG",
                "LA",
                "LV",
                "LB",
                "LS",
                "LR",
                "LY",
                "LI",
                "LT",
                "LU",
                "MO",
                "MK",
                "MG",
                "MW",
                "MY",
                "MV",
                "ML",
                "MT",
                "MH",
                "MQ",
                "MR",
                "MU",
                "YT",
                "MX",
                "FM",
                "MD",
                "MC",
                "MN",
                "MS",
                "MA",
                "MZ",
                "MM",
                "NA",
                "NR",
                "NP",
                "NL",
                "AN",
                "NC",
                "NZ",
                "NI",
                "NE",
                "NG",
                "NU",
                "NF",
                "MP",
                "NO",
                "OM",
                "PK",
                "PW",
                "PS",
                "PA",
                "PG",
                "PY",
                "PE",
                "PH",
                "PN",
                "PL",
                "PT",
                "PR",
                "QA",
                "RE",
                "RO",
                "RU",
                "RW",
                "SH",
                "KN",
                "LC",
                "PM",
                "VC",
                "WS",
                "SM",
                "ST",
                "SA",
                "SN",
                "CS",
                "SC",
                "SL",
                "SG",
                "SK",
                "SI",
                "SB",
                "SO",
                "ZA",
                "GS",
                "ES",
                "LK",
                "SD",
                "SR",
                "SJ",
                "SZ",
                "SE",
                "CH",
                "SY",
                "TW",
                "TJ",
                "TZ",
                "TH",
                "TL",
                "TG",
                "TK",
                "TO",
                "TT",
                "TN",
                "TR",
                "TM",
                "TC",
                "TV",
                "UG",
                "UA",
                "AE",
                "GB",
                "US",
                "UM",
                "UY",
                "UZ",
                "VU",
                "VE",
                "VN",
                "VG",
                "VI",
                "WF",
                "EH",
                "YE",
                "ZM",
                "ZW"
            );
    }


    public function load(ObjectManager $manager)
    {
//        $superUser = new User();
//        $superUser->setEmail('daniyar.san@gmail.com');
//        $superUser->setPassword($this->encoder->encodePassword($superUser, '121212'));
//        $superUser->setRoles(['ROLE_ADMIN']);
//        $manager->persist($superUser);
//        $manager->flush();

        for ($i = 1; $i <= 20; $i++) {
            $user = new User();
            $user->setPassword($this->encoder->encodePassword($user, '121212'));
            $user->setEmail('profile' . $i . '@gmail.com');
            $user->setRoles(['ROLE_USER']);
            if ($i > 20) {
                $user->setEmail('company' . $i . '@gmail.com');
                $user->setRoles(['ROLE_COMPANY']);
                $company = new Company();
                $company->setName('company' . $i);
                $company->setIsVerified(true);
                $company->setCountry('US');
                $user->setCompany($company);
            } else {
                $profile = new Profile();
                $profile->setFirstName($this->getRandomName());
                $profile->setLastName($this->getRandomLastName());
                $profile->setCountry('US');
                $user->setProfile($profile);
            }
            $manager->persist($user);
        }
        //        $manager->flush();

    }

    protected function getRandomName() {
        return $this->name[array_rand($this->name)];
    }

    protected function getRandomLastName() {
        return $this->lastName[array_rand($this->lastName)];
    }

    protected function getRandomCompanyName() {}
}
