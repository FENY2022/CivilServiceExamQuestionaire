<?php
declare(strict_types=1);

function category_definitions(string $type): array
{
    $general = [
        ['grammar', 'Grammar and Correct Usage', 'verbal'],
        ['vocabulary', 'Vocabulary', 'verbal'],
        ['reading', 'Reading Comprehension', 'verbal'],
        ['paragraph', 'Paragraph Organization', 'verbal'],
        ['basic_math', 'Basic Mathematics', 'numerical'],
        ['percentages', 'Fractions, Decimals, and Percentages', 'numerical'],
        ['ratio', 'Ratio and Proportion', 'numerical'],
        ['word_problems', 'Word Problems', 'numerical'],
        ['data_interpretation', 'Data Interpretation', 'numerical'],
        ['logic', 'Logic', 'analytical'],
        ['problem_solving', 'Problem-solving', 'analytical'],
        ['assumptions', 'Identifying Assumptions', 'analytical'],
        ['conclusions', 'Drawing Conclusions', 'analytical'],
        ['patterns', 'Sequence and Pattern Recognition', 'analytical'],
        ['constitution', 'Philippine Constitution', 'general_information'],
        ['ra6713', 'RA 6713 Code of Conduct', 'general_information'],
        ['human_rights', 'Peace and Human Rights', 'general_information'],
        ['environment', 'Environment Management and Protection', 'general_information'],
        ['current_events', 'Current Events and Government Programs', 'general_information'],
        ['history', 'Basic Philippine History', 'general_information'],
    ];

    $professional = [
        ['advanced_reasoning', 'Advanced Analytical Reasoning', 'professional'],
        ['complex_verbal_numerical', 'Complex Verbal and Numerical Questions', 'professional'],
        ['decision_making', 'Decision-making and Critical Thinking', 'professional'],
    ];

    $clerical = [
        ['filing', 'Filing', 'clerical'],
        ['alphabetizing', 'Alphabetizing', 'clerical'],
        ['office_procedures', 'Office Procedures', 'clerical'],
        ['records_management', 'Records Management', 'clerical'],
    ];

    return $type === 'professional' ? array_merge($general, $professional) : array_merge($general, $clerical);
}

function get_exam_questions(string $type): array
{
    $timeLimit = $type === 'professional' ? 190 : 160;
    $targetTotal = $type === 'professional' ? 170 : 165;
    $categories = [];
    foreach (category_definitions($type) as [$key, $title, $group]) {
        $categories[] = [
            'key' => $key,
            'title' => $title,
            'group' => $group,
            'questions' => build_twenty_questions($key, $title),
        ];
    }
    $categories = select_random_categories($categories, $targetTotal);

    return [
        'type' => $type,
        'title' => $type === 'professional' ? 'Professional Level Practice Exam' : 'Subprofessional Level Practice Exam',
        'timeLimitMinutes' => $timeLimit,
        'passingPercent' => 80,
        'categories' => $categories,
    ];
}

function select_random_categories(array $categories, int $targetTotal): array
{
    $totalAvailable = array_sum(array_map(fn($category) => count($category['questions']), $categories));
    if ($targetTotal >= $totalAvailable) {
        foreach ($categories as &$category) {
            shuffle($category['questions']);
        }
        unset($category);
        return $categories;
    }

    $allocations = [];
    $remaining = $targetTotal;
    foreach ($categories as $index => $category) {
        $available = count($category['questions']);
        $share = (int)floor(($available / $totalAvailable) * $targetTotal);
        $share = max(1, min($available, $share));
        $allocations[$index] = $share;
        $remaining -= $share;
    }

    while ($remaining > 0) {
        foreach ($categories as $index => $category) {
            if ($remaining <= 0) {
                break;
            }
            if ($allocations[$index] < count($category['questions'])) {
                $allocations[$index]++;
                $remaining--;
            }
        }
    }

    while ($remaining < 0) {
        foreach ($categories as $index => $category) {
            if ($remaining >= 0) {
                break;
            }
            if ($allocations[$index] > 1) {
                $allocations[$index]--;
                $remaining++;
            }
        }
    }

    foreach ($categories as $index => &$category) {
        shuffle($category['questions']);
        $category['questions'] = array_slice($category['questions'], 0, $allocations[$index]);
    }
    unset($category);
    shuffle($categories);

    return $categories;
}

function build_twenty_questions(string $key, string $title): array
{
    $bank = base_question_bank($key);
    $questions = [];
    for ($i = 0; $i < 20; $i++) {
        $item = $bank[$i % count($bank)];
        $item['id'] = $key . '_' . ($i + 1);
        if ($i >= count($bank)) {
            $item['question'] .= ' (Practice set ' . (int)floor($i / count($bank) + 1) . ')';
        }
        $item['category'] = $title;
        $questions[] = $item;
    }
    return $questions;
}

function q(string $question, array $choices, int $answer, string $explanation): array
{
    return compact('question', 'choices', 'answer', 'explanation');
}

function base_question_bank(string $key): array
{
    $banks = [
        'grammar' => [
            q('Choose the correct sentence.', ['She do her work carefully.', 'She does her work carefully.', 'She doing her work carefully.', 'She done her work carefully.'], 1, 'Singular subject she uses does.'),
            q('Fill in the blank: The committee ___ decided to postpone the meeting.', ['has', 'have', 'are', 'were'], 0, 'Committee is treated as a singular collective noun.'),
            q('Which word correctly completes the sentence: This report is between you and ___.', ['I', 'me', 'myself', 'mine'], 1, 'Between is a preposition and takes the object pronoun me.'),
            q('Choose the correctly punctuated sentence.', ['However we must continue.', 'However, we must continue.', 'However we, must continue.', 'However; we must continue.'], 1, 'Introductory however is followed by a comma.'),
            q('Choose the correct verb: Neither the clerk nor the officers ___ available.', ['is', 'are', 'was', 'be'], 1, 'With neither/nor, the verb agrees with the nearer subject officers.'),
        ],
        'vocabulary' => [
            q('PANACEA most nearly means:', ['cure-all', 'conflict', 'standard', 'warning'], 0, 'A panacea is a remedy for all problems.'),
            q('DEBACLE most nearly means:', ['introduction', 'disaster', 'agreement', 'delay'], 1, 'Debacle means a sudden failure or disaster.'),
            q('ALLEGIANCE most nearly means:', ['loyalty', 'anger', 'wealth', 'silence'], 0, 'Allegiance means loyalty or commitment.'),
            q('CANTANKEROUS most nearly means:', ['generous', 'quarrelsome', 'careful', 'simple'], 1, 'Cantankerous means bad-tempered or quarrelsome.'),
            q('PROSCRIBE most nearly means:', ['forbid', 'announce', 'decorate', 'repair'], 0, 'Proscribe means to prohibit or forbid.'),
        ],
        'reading' => [
            q('A passage says pollutants cross national borders through wind. What is the best conclusion?', ['Pollution can affect distant places.', 'Pollution stays in one country.', 'Wind prevents pollution.', 'Only cities are polluted.'], 0, 'The main idea is that pollutants can travel far.'),
            q('Critical reading requires slowing down and writing questions in the margin. What does this imply?', ['Readers should be active.', 'Readers should memorize all words.', 'Reading is unnecessary.', 'Questions reduce understanding.'], 0, 'The passage emphasizes active participation.'),
            q('If a text says 90% of lightning victims survive, what can be inferred?', ['Most victims survive.', 'All victims die.', 'Lightning is cold.', 'Lightning cannot strike twice.'], 0, 'Ninety percent is a majority.'),
            q('A cave passage says horses appear in more than 300 wall images, more than all other animals. Which animal appears most?', ['Birds', 'Bison', 'Horses', 'Wild cats'], 2, 'The passage directly says horses outnumber other animals.'),
            q('In a passage, the phrase pays heed to is closest to:', ['notices', 'buys', 'repairs', 'records'], 0, 'To pay heed means to notice or give attention.'),
        ],
        'paragraph' => [
            q('Arrange: A. Diversify investments. B. Expand money to different wheels. C. Do not put all eggs in one basket. D. Consider risks.', ['CBAD', 'CDAB', 'ADCB', 'BCAD'], 1, 'C introduces the idea, D adds caution, A gives method, B concludes.'),
            q('Arrange: A. Design layout. B. Register domain. C. Add content. D. Choose hosting.', ['BADC', 'BDAC', 'ABCD', 'DCBA'], 1, 'Website building usually starts with domain, then hosting, design, content.'),
            q('Arrange: A. Speaking daily helps. B. Watching English shows helps. C. Reading helps. D. There are many ways to improve English.', ['DCBA', 'DABC', 'BACD', 'ACDB'], 0, 'D introduces the topic, followed by examples.'),
            q('Arrange: A. Some know their dream early. B. Choosing a course is challenging. C. Others are undecided. D. Destiny is choice.', ['BACD', 'BCAD', 'ABCD', 'CBAD'], 1, 'B introduces the challenge, then contrasts groups, then concludes.'),
            q('Which sentence should usually come first in a paragraph?', ['A supporting example', 'A concluding phrase', 'A topic sentence', 'A repeated detail'], 2, 'A topic sentence introduces the main idea.'),
        ],
        'basic_math' => [
            q('What is 4 x 8 + 12 / 4 - 8 / 2?', ['30', '31', '35', '39'], 1, 'Apply order of operations: 32 + 3 - 4 = 31.'),
            q('What is (25)^0 - (-2)^4 + (-81) - (-8)^2?', ['160', '-160', '1', '2'], 1, '1 - 16 - 81 - 64 = -160.'),
            q('Round 9,750 to the nearest hundred.', ['9,700', '9,800', '9,900', '9,760'], 1, 'The tens digit is 5, so round up to 9,800.'),
            q('What is 4010 x 0.0001?', ['0.401', '4.01', '0.0401', '40.1'], 0, 'Move the decimal four places left.'),
            q('What is the average of 7/8 and 3/4?', ['13/16', '5/6', '5/12', '13/8'], 0, '(7/8 + 6/8) / 2 = 13/16.'),
        ],
        'percentages' => [
            q('20% of 30% of X is 225. Find X.', ['375', '112.5', '3750', '825'], 2, '0.20 x 0.30 x X = 225, so X = 3750.'),
            q('44 is 80% of what number?', ['50', '55', '60', '70'], 1, '44 / 0.80 = 55.'),
            q('10% of 300 is what number?', ['3', '30', '0.3', '300'], 1, '0.10 x 300 = 30.'),
            q('120 is what percent of 48?', ['25%', '40%', '250%', '2.5%'], 2, '120 / 48 = 2.5 = 250%.'),
            q('If 95% of 160 people have cars, how many do not?', ['8', '16', '152', '95'], 0, '5% of 160 = 8.'),
        ],
        'ratio' => [
            q('The ratio of girls to boys is 12 to 9. Simplify.', ['3:4', '4:3', '9:24', '1:2'], 1, 'Divide both terms by 3.'),
            q('The ratio of 750m to 3km is:', ['1:4', '4:1', '3:5', '2:7'], 0, '3km = 3000m, so 750:3000 = 1:4.'),
            q('A parallelogram has side ratio 4:3 and perimeter 56cm. Find the sides.', ['16cm and 12cm', '14cm and 13cm', '18cm and 15cm', '30cm and 26cm'], 0, '2(4x+3x)=56, x=4.'),
            q('0.75 is to 3/4 as 0.8 is to:', ['2/3', '4/5', '5/6', '3/5'], 1, '0.8 = 8/10 = 4/5.'),
            q('2 is to 50 as 3.2 is to:', ['60', '70', '80', '90'], 2, 'Multiply by 25: 3.2 x 25 = 80.'),
        ],
        'word_problems' => [
            q('Three siomai cost P15. How much do 12 siomai cost?', ['45', '55', '60', '80'], 2, 'Each costs P5, so 12 cost P60.'),
            q('A cyclist travels 6 kph for 30 minutes. How far did he travel?', ['3 km', '6 km', '12 km', '18 km'], 0, '30 minutes is 0.5 hour. 6 x 0.5 = 3 km.'),
            q('A group has 120 persons and 32 more women than men. How many women?', ['44', '56', '76', '88'], 2, 'Men = 44, women = 76.'),
            q('Four consecutive integers have a mean of 9.5. Largest integer?', ['10', '11', '14', '19'], 1, 'The integers are 8, 9, 10, and 11.'),
            q('A population of 5,000 increases 5% yearly for 10 years. Approximate population?', ['5,250', '7,500', '8,144', '12,500'], 2, '5000 x 1.05^10 is about 8,144.'),
        ],
        'data_interpretation' => [
            q('If 40% passed an exam and one-fourth passed on first trial, what percent passed on second trial?', ['10%', '15%', '30%', '75%'], 2, 'Three-fourths of 40% is 30%.'),
            q('If a value falls by 7%, what multiplier is used?', ['0.07', '0.70', '0.93', '1.07'], 2, 'A 7% reduction leaves 93%.'),
            q('If profit is 60 and sales are 345, return on sales is approximately:', ['17.4%', '11.5%', '13%', '30%'], 0, '60 / 345 is about 17.4%.'),
            q('A chart shows 20% passed in 2008. What percent failed?', ['20%', '40%', '60%', '80%'], 3, '100% - 20% = 80%.'),
            q('If 25% of a 5% allocation is for education, equivalent share is:', ['1.25%', '5%', '25%', '30%'], 0, '0.25 x 5% = 1.25%.'),
        ],
        'logic' => [
            q('All luxuries are needless expenditures. Cable TV is a luxury. Therefore:', ['Cable TV is needless expenditure.', 'All expenditures are luxuries.', 'Cable TV is free.', 'No conclusion.'], 0, 'This follows by syllogism.'),
            q('Only one factory worker has exactly five children. A worker has five children. Therefore:', ['All workers have five children.', 'Only that worker has exactly five children.', 'No worker has children.', 'Some have six children.'], 1, 'The premise states uniqueness.'),
            q('All tulips are white and all pansies are yellow. All flowers are white or yellow is:', ['True', 'False', 'Uncertain', 'Impossible'], 2, 'Other flower types are not described.'),
            q('Useful things are valuable. A toothpick is useful. Therefore:', ['A toothpick is valuable.', 'A toothpick is useless.', 'All valuable things are toothpicks.', 'No conclusion.'], 0, 'It follows directly.'),
            q('Search is to find as explore is to:', ['discover', 'sleep', 'paint', 'walk'], 0, 'One searches to find and explores to discover.'),
        ],
        'problem_solving' => [
            q('If twice a number plus 8 is 40, find the number.', ['8', '16', '24', '32'], 1, '2x + 8 = 40, so x = 16.'),
            q('If x - 3 = y, what is (y - x)^3?', ['9', '-27', '27', '81'], 1, 'y - x = -3, so cube is -27.'),
            q('If the area stays constant and one side increases by 1/4, the other side:', ['decreases by 1/5', 'decreases by 1/4', 'increases by 1/5', 'doubles'], 0, 'New side must be 4/5 of original.'),
            q('If 300 took forms, 280 appeared, and 70% passed, how many failed the test?', ['20', '54', '84', '104'], 2, '30% of 280 failed = 84.'),
            q('What smallest positive number divided by 3, 4, or 5 leaves remainder 2?', ['22', '42', '62', '122'], 2, 'LCM is 60; add 2.'),
        ],
        'assumptions' => [
            q('A policy says online review improves access. Which assumption is needed?', ['Learners can access the internet.', 'All learners are experts.', 'Books are banned.', 'Exams are cancelled.'], 0, 'Online access depends on internet availability.'),
            q('A memo requires ID at entry to improve security. What is assumed?', ['IDs help identify authorized persons.', 'All visitors are employees.', 'Security is unnecessary.', 'IDs are always fake.'], 0, 'The policy relies on identification value.'),
            q('A city adds recycling bins to reduce waste. What is assumed?', ['People will use the bins properly.', 'Waste will disappear instantly.', 'Bins create waste.', 'Recycling is illegal.'], 0, 'The program needs user cooperation.'),
            q('A reviewer adds timers to improve exam readiness. What is assumed?', ['Timed practice helps preparation.', 'Timers answer questions.', 'Time limits do not exist.', 'Scores are random.'], 0, 'Timed practice mirrors exam conditions.'),
            q('An agency trains staff in ethics to reduce misconduct. What is assumed?', ['Training can affect behavior.', 'Misconduct is required.', 'Ethics rules are secret.', 'Training removes all rules.'], 0, 'The plan assumes training improves conduct.'),
        ],
        'conclusions' => [
            q('TV makes viewers fear crime and passively accept events. Best conclusion:', ['TV promotes helpless vulnerability.', 'TV prevents all crime.', 'TV viewers never fear.', 'Crime is fictional.'], 0, 'This combines the two stated effects.'),
            q('Research in needy countries often follows Western models instead of local needs. Best conclusion:', ['Research may miss local objectives.', 'Research is always useless.', 'Western models are illegal.', 'Local needs do not matter.'], 0, 'The issue is mismatch with local needs.'),
            q('Money value affects long-range planning. Predicting money value aids progress. Conclusion:', ['Financial predictability helps economic planning.', 'Planning is impossible.', 'Money has no value.', 'Progress prevents planning.'], 0, 'The statement directly supports predictability.'),
            q('A peso decline caused crisis, layoffs, and price increases. Conclusion:', ['The crisis affected employment and prices.', 'Prices fell sharply.', 'Jobs increased.', 'Industries became immune.'], 0, 'The paragraph states unemployment and price increases.'),
            q('Two people see mud and stars from the same bars. Conclusion:', ['Perspective differs.', 'Both are blind.', 'Bars are invisible.', 'Stars are mud.'], 0, 'The statement is about differing viewpoints.'),
        ],
        'patterns' => [
            q('125%, 100%, 80%, 64%, __', ['51.2%', '52.5%', '60%', '50%'], 0, 'Each term is multiplied by 0.8.'),
            q('2, 10, 60, 420, __', ['840', '1680', '3360', '4500'], 2, 'Multiply by 5, 6, 7, then 8.'),
            q('1, 2, 2, 3, 4, 4, 8, 5, __', ['6', '7', '16', '20'], 2, 'Alternate series doubles: 1, 2, 4, 8, 16.'),
            q('15, 7.5, 7.5, 15, 60, __', ['120', '240', '420', '480'], 3, 'Multiply by 0.5, 1, 2, 4, 8.'),
            q('[4/4x2], [8/4x2], [16/4x2], __', ['2', '8', '16', '32'], 2, 'Next is [32/4x2] = 16.'),
        ],
        'constitution' => [
            q('What is the national language under the 1987 Constitution?', ['Pilipino', 'Tagalog', 'Filipino', 'English'], 2, 'Article XIV, Section 6 states the national language is Filipino.'),
            q('What is the introductory part of the Constitution called?', ['Preface', 'Preamble', 'Bill of Rights', 'Amendments'], 1, 'The introductory declaration is the Preamble.'),
            q('What form of government does the Philippines adopt?', ['Republican and democratic', 'Monarchical', 'Federal only', 'Military'], 0, 'The Constitution states democratic and republican.'),
            q('Who may issue a warrant of arrest or search warrant?', ['Senator', 'Judge', 'President', 'Governor'], 1, 'Warrants are issued by judges.'),
            q('The writ of habeas corpus may be suspended in invasion or:', ['rebellion', 'inflation', 'election', 'audit'], 0, 'The Constitution mentions invasion or rebellion.'),
        ],
        'ra6713' => [
            q('RA 6713 is known as:', ['Code of Conduct and Ethical Standards', 'Local Government Code', 'Labor Code', 'Election Code'], 0, 'RA 6713 sets ethical standards for public officials and employees.'),
            q('Which is a norm under RA 6713?', ['Commitment to public interest', 'Private gain first', 'Delay all services', 'Avoid transparency'], 0, 'Public interest is a core norm.'),
            q('Public officials should process documents:', ['promptly and efficiently', 'only for friends', 'with unnecessary delay', 'without records'], 0, 'Responsiveness to the public is required.'),
            q('Which must be filed by public officials as required by law?', ['SALN', 'Passport', 'Driver license', 'Barangay clearance only'], 0, 'SALN means Statement of Assets, Liabilities, and Net Worth.'),
            q('Gifts that create conflict of interest should be:', ['avoided', 'demanded', 'hidden', 'sold'], 0, 'RA 6713 discourages improper gifts and conflicts.'),
        ],
        'human_rights' => [
            q('Human rights are best described as rights that are:', ['inherent to all persons', 'only for officials', 'sold by agencies', 'temporary favors'], 0, 'Human rights belong to all persons by dignity.'),
            q('Peaceful settlement of disputes promotes:', ['social harmony', 'violence', 'corruption', 'delay only'], 0, 'Peaceful resolution supports order and harmony.'),
            q('Due process means a person should be given:', ['fair hearing', 'automatic punishment', 'secret trial', 'no notice'], 0, 'Due process includes notice and opportunity to be heard.'),
            q('Equal protection means laws should be applied:', ['fairly to similarly situated persons', 'only to the rich', 'randomly', 'secretly'], 0, 'Equal protection guards against unjust discrimination.'),
            q('Respect for dignity is central to:', ['human rights', 'traffic coding', 'procurement forms', 'inventory labels'], 0, 'Human dignity grounds human rights.'),
        ],
        'environment' => [
            q('Proper waste segregation helps:', ['solid waste management', 'water pollution', 'illegal logging', 'overpricing'], 0, 'Segregation supports solid waste management.'),
            q('Planting trees primarily helps reduce:', ['soil erosion', 'office filing', 'tax rates', 'email spam'], 0, 'Trees stabilize soil and absorb carbon.'),
            q('Which practice conserves water?', ['Fixing leaks', 'Leaving taps open', 'Dumping waste', 'Burning plastic'], 0, 'Fixing leaks prevents water waste.'),
            q('Environmental impact assessment is used before projects to identify:', ['environmental effects', 'employee birthdays', 'exam scores', 'email passwords'], 0, 'EIA evaluates potential environmental impacts.'),
            q('Recycling primarily reduces:', ['waste sent to landfills', 'reading time', 'constitutional rights', 'office attendance'], 0, 'Recycling diverts reusable materials.'),
        ],
        'current_events' => [
            q('Which agency leads national civil service policies?', ['Civil Service Commission', 'Department of Tourism', 'COMELEC', 'BIR'], 0, 'CSC oversees civil service rules and eligibility.'),
            q('PhilSys refers to the national:', ['ID system', 'airport system', 'sports league', 'weather bureau'], 0, 'PhilSys is the Philippine Identification System.'),
            q('Digital government services aim to improve:', ['access and efficiency', 'paper waste', 'longer queues', 'manual duplication'], 0, 'Digitalization improves service delivery.'),
            q('Disaster risk reduction is important because the Philippines is prone to:', ['typhoons and earthquakes', 'snowstorms only', 'desert drought only', 'none'], 0, 'The country faces multiple natural hazards.'),
            q('A government transparency portal supports:', ['public accountability', 'secret spending', 'fake records', 'private monopoly'], 0, 'Transparency supports accountability.'),
        ],
        'history' => [
            q('Who is known as the national hero of the Philippines?', ['Jose Rizal', 'Ferdinand Magellan', 'William Taft', 'Douglas MacArthur'], 0, 'Jose Rizal is widely recognized as the national hero.'),
            q('Independence Day is celebrated on:', ['June 12', 'July 4', 'December 30', 'August 21'], 0, 'Philippine Independence Day is June 12.'),
            q('The Katipunan was founded to pursue:', ['independence from Spain', 'tax collection', 'foreign trade only', 'sports training'], 0, 'The Katipunan sought independence.'),
            q('The EDSA People Power Revolution occurred in:', ['1986', '1898', '1946', '1972'], 0, 'The first EDSA People Power Revolution was in 1986.'),
            q('The Cry of Pugad Lawin is associated with:', ['Philippine Revolution', 'World War II surrender', 'first election', 'ASEAN founding'], 0, 'It is linked to the start of the revolution against Spain.'),
        ],
        'advanced_reasoning' => [
            q('If all A are B and some B are C, which must be true?', ['Some A are C', 'All C are A', 'No A are C', 'None of these must be true'], 3, 'Some B being C does not guarantee any A is C.'),
            q('A policy works only if funding and staffing are both sufficient. Funding is insufficient. Conclusion:', ['The policy cannot work as planned.', 'Staffing is sufficient.', 'The policy is complete.', 'Funding is irrelevant.'], 0, 'A necessary condition is missing.'),
            q('If statement X is true only when Y is false, and Y is true, then X is:', ['false', 'true', 'uncertain', 'both true and false'], 0, 'X requires Y to be false.'),
            q('Which weakens the claim that longer training caused better scores?', ['Trainees were already higher performers before training.', 'Training had lectures.', 'Scores were measured.', 'Participants attended.'], 0, 'Preexisting ability offers an alternate explanation.'),
            q('Which is the strongest evidence for a program effect?', ['Comparable control group improved less', 'One testimonial', 'A poster was printed', 'The office liked it'], 0, 'Comparison with a control group supports causal inference.'),
        ],
        'complex_verbal_numerical' => [
            q('If profit is 25% of sales of 2,000,000 and shared among 4 owners, each gets:', ['125,000', '312,500', '500,000', '1,250,000'], 0, 'Profit is 500,000; divided by 4 is 125,000.'),
            q('If (a+b)^2=25 and (a-b)^2=45, find a^2+b^2.', ['35', '70', '625', '2025'], 0, 'Adding gives 2a^2+2b^2=70.'),
            q('Choose the pair closest in relation: Tailor is to suit as editor is to:', ['manuscript', 'building', 'vehicle', 'garden'], 0, 'Both alter or improve the object.'),
            q('If a passage uses however, it usually signals:', ['contrast', 'repetition only', 'definition only', 'a list'], 0, 'However introduces contrast.'),
            q('A car travels 120 miles at 40 mph and returns at 60 mph. Average speed?', ['44', '46', '48', '50'], 2, 'Total distance 240, total time 5 hours, average 48.'),
        ],
        'decision_making' => [
            q('A citizen submits incomplete documents. Best action?', ['Politely explain missing requirements and next steps.', 'Reject without explanation.', 'Ignore the citizen.', 'Ask for an unofficial fee.'], 0, 'Public service requires clear, ethical assistance.'),
            q('You discover a conflict of interest in procurement. Best action?', ['Disclose and inhibit as required.', 'Proceed secretly.', 'Hide the connection.', 'Approve immediately.'], 0, 'Disclosure protects integrity.'),
            q('Two urgent tasks arrive. Best first step?', ['Assess deadlines, impact, and instructions.', 'Do the easiest only.', 'Ignore both.', 'Delegate without briefing.'], 0, 'Prioritization should be reasoned.'),
            q('A coworker asks you to alter public records. Best response?', ['Refuse and report through proper channels.', 'Alter records as favor.', 'Delete files.', 'Accept if paid.'], 0, 'Records integrity is required.'),
            q('An applicant complains about delay. Best action?', ['Check status and provide accurate update.', 'Blame another office only.', 'Hide the file.', 'Tell them not to return.'], 0, 'Responsive service requires accurate assistance.'),
        ],
        'filing' => [
            q('In alphabetical filing, Dela Cruz, Juan should be filed under:', ['D', 'J', 'C', 'M'], 0, 'Use the surname first.'),
            q('A numeric filing system arranges records by:', ['assigned numbers', 'favorite color', 'file thickness', 'random order'], 0, 'Numeric filing uses numbers as primary keys.'),
            q('Chronological filing arranges records by:', ['date', 'surname', 'amount', 'department color'], 0, 'Chronological means date order.'),
            q('A good filing system should be:', ['consistent and easy to retrieve', 'secret and random', 'duplicated everywhere', 'unlabeled'], 0, 'Retrieval and consistency are key.'),
            q('A file guide is used to:', ['divide and locate file sections', 'print invoices', 'send email', 'count cash'], 0, 'Guides organize file drawers.'),
        ],
        'alphabetizing' => [
            q('Which comes first alphabetically?', ['Abao', 'Abaya', 'Abella', 'Acosta'], 0, 'Compare letters from left to right.'),
            q('Arrange first: Cruz, Castro, Cabrera, Co.', ['Cabrera', 'Castro', 'Co', 'Cruz'], 0, 'Cab comes before Cas, Co, and Cru.'),
            q('For names with prefixes, office rules should be applied:', ['consistently', 'randomly', 'only sometimes', 'never'], 0, 'Consistency prevents retrieval errors.'),
            q('Which comes first: Santos or Santiago?', ['Santiago', 'Santos', 'Same', 'Cannot file'], 0, 'Santia comes before Santo.'),
            q('Which comes last: Lim, Lopez, Luna, Lao?', ['Luna', 'Lopez', 'Lim', 'Lao'], 0, 'Lu comes after Lo, Li, and La.'),
        ],
        'office_procedures' => [
            q('Best way to answer a company phone call:', ['State company or department politely.', 'Say hello and wait.', 'Let it ring.', 'Answer rudely.'], 0, 'Professional calls identify the office politely.'),
            q('CC in an email means:', ['copy furnished', 'carbon computer', 'closed copy', 'client command'], 0, 'CC means carbon copy/copy furnished.'),
            q('A soft copy is:', ['digital file', 'printed paper', 'xerox copy', 'signed hardbound book'], 0, 'Soft copy is electronic.'),
            q('A fax machine transmits:', ['scanned documents through phone lines', 'cash payments', 'office chairs', 'water bills only'], 0, 'Fax sends scanned text/images.'),
            q('Which program is commonly used for email?', ['Outlook', 'Excel', 'Paint', 'Calculator'], 0, 'Outlook is an email client.'),
        ],
        'records_management' => [
            q('Records management includes:', ['creation, storage, retrieval, and disposal', 'only decoration', 'only lunch schedules', 'private chatting'], 0, 'It covers the records life cycle.'),
            q('Confidential records should be:', ['protected from unauthorized access', 'posted publicly', 'left on counters', 'emailed to everyone'], 0, 'Confidentiality requires access control.'),
            q('A retention schedule tells how long records are:', ['kept before disposal or archiving', 'printed in blue', 'read aloud', 'renamed daily'], 0, 'Retention schedules set storage periods.'),
            q('Backups are important because they:', ['protect against data loss', 'increase errors', 'replace all filing rules', 'remove passwords'], 0, 'Backups help recover records.'),
            q('A sales invoice is issued to show:', ['products, quantities, and prices sold', 'employee leave only', 'weather report', 'exam scores'], 0, 'Invoices document sale details.'),
        ],
    ];

    return $banks[$key] ?? [
        q('Sample question for ' . $key . '.', ['Option A', 'Option B', 'Option C', 'Option D'], 0, 'This is a generated sample item.'),
    ];
}
