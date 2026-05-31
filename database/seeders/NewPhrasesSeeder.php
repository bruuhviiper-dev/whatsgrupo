<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StatusPhrase;

class NewPhrasesSeeder extends Seeder
{
    public function run(): void
    {
        $phrases = [
            // Categoria: Amor
            ['category' => 'amor', 'author' => 'Vinicius de Moraes', 'phrase' => 'Que não seja imortal, posto que é chama, mas que seja infinito enquanto dure.'],
            ['category' => 'amor', 'author' => 'Carlos Drummond de Andrade', 'phrase' => 'Amor é privilégio de maduros: estendidos na mais estreita cama, que se torna a pastagem mais vasta.'],
            ['category' => 'amor', 'author' => 'Antoine de Saint-Exupéry', 'phrase' => 'Amar não é olhar um para o outro, é olhar juntos na mesma direção.'],
            ['category' => 'amor', 'author' => 'Mário Quintana', 'phrase' => 'Amar é mudar a alma de casa.'],
            ['category' => 'amor', 'author' => 'Clarice Lispector', 'phrase' => 'Amar os outros é a única salvação individual que conheço: ninguém estará perdido se der amor.'],
            ['category' => 'amor', 'author' => 'Luís Vaz de Camões', 'phrase' => 'Amor é fogo que arde sem se ver; é ferida que dói, e não se sente.'],
            ['category' => 'amor', 'author' => 'Pablo Neruda', 'phrase' => 'Amo-te sem saber como, nem quando, nem onde, amo-te diretamente sem problemas nem orgulho.'],
            ['category' => 'amor', 'author' => 'Fernando Pessoa', 'phrase' => 'Amo como ama o amor. Não conheço nenhuma outra razão para amar senão amar.'],
            ['category' => 'amor', 'author' => 'Florbela Espanca', 'phrase' => 'Eu quero amar, amar perdidamente! Amar só por amar: Aqui... além... Mais Este e Aquele, o Outro e toda a gente...'],
            ['category' => 'amor', 'author' => 'William Shakespeare', 'phrase' => 'O amor não se vê com os olhos mas com o coração.'],
            ['category' => 'amor', 'author' => 'Machado de Assis', 'phrase' => 'Cada qual sabe amar a seu modo; o modo, pouco importa; o essencial é que saiba amar.'],
            ['category' => 'amor', 'author' => 'Victor Hugo', 'phrase' => 'A suprema felicidade da vida é a convicção de ser amado por aquilo que você é.'],
            ['category' => 'amor', 'author' => 'Gabriel García Márquez', 'phrase' => 'O amor é tão importante como a comida. Mas não alimenta.'],
            ['category' => 'amor', 'author' => 'Cecília Meireles', 'phrase' => 'O amor é a única coisa que cresce à medida que se reparte.'],
            ['category' => 'amor', 'author' => 'Platão', 'phrase' => 'Ao toque do amor, todo mundo se torna um poeta.'],
            ['category' => 'amor', 'author' => 'Cora Coralina', 'phrase' => 'O verdadeiro amor nunca se desgasta. Quanto mais se dá mais se tem.'],
            ['category' => 'amor', 'author' => 'Mahatma Gandhi', 'phrase' => 'Onde há amor, há vida.'],
            ['category' => 'amor', 'author' => 'Albert Einstein', 'phrase' => 'A gravidade não é responsável por as pessoas se apaixonarem.'],
            ['category' => 'amor', 'author' => 'Oscar Wilde', 'phrase' => 'Manter o amor no coração; uma vida sem ele é como um jardim sem sol.'],
            ['category' => 'amor', 'author' => 'Rubem Alves', 'phrase' => 'O amor é esta descoberta: de que o outro é fundamental para a gente viver.'],

            // Categoria: Amizade
            ['category' => 'amizade', 'author' => 'Mario Quintana', 'phrase' => 'A amizade é um amor que nunca morre.'],
            ['category' => 'amizade', 'author' => 'Aristóteles', 'phrase' => 'A amizade é uma alma com dois corpos.'],
            ['category' => 'amizade', 'author' => 'William Shakespeare', 'phrase' => 'Um amigo é alguém que te conhece tal como és, compreende onde tens estado, aceita o que te tornaste e ainda assim, permite-te crescer.'],
            ['category' => 'amizade', 'author' => 'C.S. Lewis', 'phrase' => 'A amizade nasce naquele momento em que uma pessoa diz para a outra: O quê? Você também? Pensei que eu fosse o único!'],
            ['category' => 'amizade', 'author' => 'Epicuro', 'phrase' => 'De todos os bens que a sabedoria proporciona para a felicidade de toda a vida, o maior de todos é a amizade.'],
            ['category' => 'amizade', 'author' => 'Henry Ford', 'phrase' => 'O meu melhor amigo é aquele que faz aflorar o melhor de mim.'],
            ['category' => 'amizade', 'author' => 'Albert Camus', 'phrase' => 'Não ande na minha frente, eu não posso seguir. Não ande atrás de mim, eu não posso liderar. Apenas ande ao meu lado e seja meu amigo.'],
            ['category' => 'amizade', 'author' => 'Sêneca', 'phrase' => 'A amizade sempre é proveitosa; o amor, no entanto, às vezes magoa.'],
            ['category' => 'amizade', 'author' => 'Ralph Waldo Emerson', 'phrase' => 'A única maneira de ter um amigo é ser um.'],
            ['category' => 'amizade', 'author' => 'Antoine de Saint-Exupéry', 'phrase' => 'Foi o tempo que dedicaste à tua rosa que a fez tão importante.'],
            ['category' => 'amizade', 'author' => 'Cícero', 'phrase' => 'A vida não é nada sem a amizade.'],
            ['category' => 'amizade', 'author' => 'Machado de Assis', 'phrase' => 'A amizade é o sentimento imortal que liga as almas para sempre.'],
            ['category' => 'amizade', 'author' => 'Charles Darwin', 'phrase' => 'A amizade de um homem é um dos melhores indicadores do seu valor.'],
            ['category' => 'amizade', 'author' => 'Mark Twain', 'phrase' => 'Bons amigos, bons livros e uma consciência sonolenta: esta é a vida ideal.'],
            ['category' => 'amizade', 'author' => 'Oscar Wilde', 'phrase' => 'Um verdadeiro amigo te esfaqueia pela frente.'],
            ['category' => 'amizade', 'author' => 'Elbert Hubbard', 'phrase' => 'Um amigo é alguém que sabe tudo sobre ti e ainda assim te ama.'],
            ['category' => 'amizade', 'author' => 'Vinicius de Moraes', 'phrase' => 'A gente não faz amigos, reconhece-os.'],
            ['category' => 'amizade', 'author' => 'Fernando Pessoa', 'phrase' => 'A amizade verdadeira não tem preços nem se troca por nada.'],
            ['category' => 'amizade', 'author' => 'Helen Keller', 'phrase' => 'Caminhar no escuro com um amigo é melhor do que caminhar sozinho na luz.'],
            ['category' => 'amizade', 'author' => 'Plutarco', 'phrase' => 'Não preciso de um amigo que mude quando eu mudo e que acene quando eu aceno; a minha sombra faz isso muito melhor.'],

            // Categoria: Motivação
            ['category' => 'motivacao', 'author' => 'Winston Churchill', 'phrase' => 'O sucesso é ir de fracasso em fracasso sem perder o entusiasmo.'],
            ['category' => 'motivacao', 'author' => 'Steve Jobs', 'phrase' => 'O único modo de fazer um excelente trabalho é amar o que você faz.'],
            ['category' => 'motivacao', 'author' => 'Theodore Roosevelt', 'phrase' => 'Acredite que você pode e você já está no meio do caminho.'],
            ['category' => 'motivacao', 'author' => 'Nelson Mandela', 'phrase' => 'Sempre parece impossível até que seja feito.'],
            ['category' => 'motivacao', 'author' => 'Walt Disney', 'phrase' => 'Todos os nossos sonhos podem se realizar, se tivermos a coragem de persegui-los.'],
            ['category' => 'motivacao', 'author' => 'Ayrton Senna', 'phrase' => 'Se você quer ser bem sucedido, precisa ter dedicação total, buscar seu último limite e dar o melhor de si.'],
            ['category' => 'motivacao', 'author' => 'Albert Einstein', 'phrase' => 'No meio da dificuldade encontra-se a oportunidade.'],
            ['category' => 'motivacao', 'author' => 'Vince Lombardi', 'phrase' => 'A perfeição não é inatingível, mas se perseguirmos a perfeição podemos alcançar a excelência.'],
            ['category' => 'motivacao', 'author' => 'Thomas Edison', 'phrase' => 'Nossa maior fraqueza está em desistir. O caminho mais certo de vencer é tentar mais uma vez.'],
            ['category' => 'motivacao', 'author' => 'Paulo Coelho', 'phrase' => 'Quando você quer alguma coisa, todo o universo conspira para que você realize o seu desejo.'],
            ['category' => 'motivacao', 'author' => 'Oprah Winfrey', 'phrase' => 'A maior aventura que você pode realizar é viver a vida dos seus sonhos.'],
            ['category' => 'motivacao', 'author' => 'Michael Jordan', 'phrase' => 'Eu errei mais de 9.000 arremessos na minha carreira. E é por isso que eu tive sucesso.'],
            ['category' => 'motivacao', 'author' => 'Bruce Lee', 'phrase' => 'Não reze por uma vida fácil, reze por força para suportar uma vida difícil.'],
            ['category' => 'motivacao', 'author' => 'Confúcio', 'phrase' => 'Não importa o quão devagar você vá, desde que você não pare.'],
            ['category' => 'motivacao', 'author' => 'Lao-Tsé', 'phrase' => 'Uma jornada de mil milhas começa com um único passo.'],
            ['category' => 'motivacao', 'author' => 'Henry Ford', 'phrase' => 'Seja você o que acredita que pode fazer, ou o que acredita que não pode fazer, você está absolutamente certo.'],
            ['category' => 'motivacao', 'author' => 'Machado de Assis', 'phrase' => 'Esquecer é uma necessidade. A vida é uma lousa, em que o destino, para escrever um novo caso, precisa de apagar o caso escrito.'],
            ['category' => 'motivacao', 'author' => 'Aristóteles', 'phrase' => 'A excelência não é um ato, mas um hábito.'],
            ['category' => 'motivacao', 'author' => 'Seneca', 'phrase' => 'Não é porque as coisas são difíceis que não ousamos; é porque não ousamos que são difíceis.'],
            ['category' => 'motivacao', 'author' => 'Ralph Waldo Emerson', 'phrase' => 'O que está atrás de nós e o que está à nossa frente são problemas menores se comparados com o que está dentro de nós.'],

            // Categoria: Engraçado
            ['category' => 'engracado', 'author' => 'Jô Soares', 'phrase' => 'Não há nada tão ruim que não possa piorar.'],
            ['category' => 'engracado', 'author' => 'Millôr Fernandes', 'phrase' => 'A preguiça é a mãe de todos os vícios. E como mãe, deve ser respeitada!'],
            ['category' => 'engracado', 'author' => 'Homer Simpson', 'phrase' => 'Se algo é difícil de fazer, então não vale a pena fazer.'],
            ['category' => 'engracado', 'author' => 'Albert Einstein', 'phrase' => 'Duas coisas são infinitas: o universo e a estupidez humana. Mas, no que respeita ao universo, ainda não adquiri a certeza absoluta.'],
            ['category' => 'engracado', 'author' => 'Oscar Wilde', 'phrase' => 'Eu posso resistir a tudo, menos à tentação.'],
            ['category' => 'engracado', 'author' => 'Mark Twain', 'phrase' => 'As rugas deveriam indicar apenas onde os sorrisos estiveram.'],
            ['category' => 'engracado', 'author' => 'Charles Chaplin', 'phrase' => 'O tempo é o melhor autor: sempre encontra um final perfeito.'],
            ['category' => 'engracado', 'author' => 'Chico Anysio', 'phrase' => 'Não tenho medo da morte. O que eu tenho é pena de perder a vida.'],
            ['category' => 'engracado', 'author' => 'George Bernard Shaw', 'phrase' => 'O dinheiro não é tudo na vida, mas ele acalma os nervos.'],
            ['category' => 'engracado', 'author' => 'Luis Fernando Verissimo', 'phrase' => 'Diga-me com quem andas e eu te direi se vou com vocês.'],
            ['category' => 'engracado', 'author' => 'Ariano Suassuna', 'phrase' => 'O otimista é um tolo. O pessimista, um chato. Bom mesmo é ser um realista esperançoso.'],
            ['category' => 'engracado', 'author' => 'Woody Allen', 'phrase' => 'Não é que eu tenha medo de morrer. É que eu não quero estar lá quando isso acontecer.'],
            ['category' => 'engracado', 'author' => 'Bob Marley', 'phrase' => 'Não cruze os braços diante de uma dificuldade, pois o maior homem do mundo morreu de braços abertos.'],
            ['category' => 'engracado', 'author' => 'Groucho Marx', 'phrase' => 'Esses são os meus princípios. Se você não gosta deles, eu tenho outros.'],
            ['category' => 'engracado', 'author' => 'Mário Quintana', 'phrase' => 'O pior dos problemas da gente é que ninguém tem nada com isso.'],
            ['category' => 'engracado', 'author' => 'Bill Gates', 'phrase' => 'Eu sempre escolherei uma pessoa preguiçosa para fazer um trabalho difícil. Porque a pessoa preguiçosa encontrará uma maneira fácil de fazê-lo.'],
            ['category' => 'engracado', 'author' => 'Steven Wright', 'phrase' => 'Emprestar dinheiro para um amigo é o mesmo que sofrer de amnésia: é melhor esquecer logo.'],
            ['category' => 'engracado', 'author' => 'Barão de Itararé', 'phrase' => 'A televisão é a maior maravilha da ciência a serviço da imbecilidade humana.'],
            ['category' => 'engracado', 'author' => 'Marilyn Monroe', 'phrase' => 'A imperfeição é bela, a loucura é genial e é melhor ser absolutamente ridículo do que absolutamente chato.'],
            ['category' => 'engracado', 'author' => 'Jim Carrey', 'phrase' => 'Atrás de todo grande homem existe uma mulher revirando os olhos.'],

            // Categoria: Reflexão
            ['category' => 'reflexao', 'author' => 'Clarice Lispector', 'phrase' => 'Renda-se, como eu me rendi. Mergulhe no que você não conhece como eu mergulhei.'],
            ['category' => 'reflexao', 'author' => 'Fernando Pessoa', 'phrase' => 'Tudo vale a pena se a alma não é pequena.'],
            ['category' => 'reflexao', 'author' => 'Santo Agostinho', 'phrase' => 'A medida do amor é amar sem medida.'],
            ['category' => 'reflexao', 'author' => 'Rene Descartes', 'phrase' => 'Penso, logo existo.'],
            ['category' => 'reflexao', 'author' => 'Sêneca', 'phrase' => 'Apressa-te a viver bem e pensa que cada dia é, por si só, uma vida.'],
            ['category' => 'reflexao', 'author' => 'Platão', 'phrase' => 'Não há ninguém, mesmo sem cultura, que não se torne poeta quando o amor toma conta dele.'],
            ['category' => 'reflexao', 'author' => 'Friedrich Nietzsche', 'phrase' => 'Aquele que tem um porquê para viver pode suportar quase qualquer como.'],
            ['category' => 'reflexao', 'author' => 'Socrates', 'phrase' => 'Só sei que nada sei.'],
            ['category' => 'reflexao', 'author' => 'Dalai Lama', 'phrase' => 'A felicidade não é algo pronto. Ela vem de suas próprias ações.'],
            ['category' => 'reflexao', 'author' => 'Immanuel Kant', 'phrase' => 'Age de tal forma que a máxima de tua ação possa ser tomada como lei universal.'],
            ['category' => 'reflexao', 'author' => 'Jean-Jacques Rousseau', 'phrase' => 'O homem nasce livre, mas por toda a parte encontra-se a ferros.'],
            ['category' => 'reflexao', 'author' => 'Voltaire', 'phrase' => 'Posso não concordar com nenhuma das palavras que você disser, mas defenderei até a morte o direito de você dizê-las.'],
            ['category' => 'reflexao', 'author' => 'Schopenhauer', 'phrase' => 'A vida é uma constante oscilação entre a ânsia de ter e o tédio de possuir.'],
            ['category' => 'reflexao', 'author' => 'Karl Marx', 'phrase' => 'Os filósofos limitaram-se a interpretar o mundo de diversas maneiras; o que importa é transformá-lo.'],
            ['category' => 'reflexao', 'author' => 'Carl Jung', 'phrase' => 'Quem olha para fora sonha; quem olha para dentro desperta.'],
            ['category' => 'reflexao', 'author' => 'Buda', 'phrase' => 'A mente é tudo. O que você pensa, você se torna.'],
            ['category' => 'reflexao', 'author' => 'Leonardo da Vinci', 'phrase' => 'A simplicidade é a sofisticação suprema.'],
            ['category' => 'reflexao', 'author' => 'Pitágoras', 'phrase' => 'Educai as crianças e não será preciso punir os homens.'],
            ['category' => 'reflexao', 'author' => 'Sigmund Freud', 'phrase' => 'Um dia, olhando para trás, os anos de luta parecerão os mais belos.'],
            ['category' => 'reflexao', 'author' => 'Stephen Hawking', 'phrase' => 'Inteligência é a capacidade de se adaptar às mudanças.'],
        ];

        foreach ($phrases as $phraseData) {
            StatusPhrase::firstOrCreate(
                ['phrase' => $phraseData['phrase']],
                [
                    'category' => $phraseData['category'],
                    'author'   => $phraseData['author'],
                    'likes'    => rand(10, 500)
                ]
            );
        }
    }
}
