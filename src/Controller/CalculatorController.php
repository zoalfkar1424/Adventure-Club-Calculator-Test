<?php
namespace App\Controller;

use App\Form\CalculatorData;
use App\Form\CalculatorType;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Cache\Adapter\RedisAdapter;
class CalculatorController extends AbstractController
{

    #[Route('/calculator', name: 'calculator')]
    public function index(Request $request)
    {
        $calculatorData = new CalculatorData();
        $form = $this->createForm(CalculatorType::class, $calculatorData);
        $form->handleRequest($request);
        return $this->render('calculator/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/get-values", name="get_values")
     */
    #[Route('/get-values', name: 'get_values')]
    public function getValues(Request $request)
    {
        $firstarg = $request->get('firstarg');
        $secondarg = $request->get('secondarg');
       $operation = $request->get('operation');
        if(is_numeric($firstarg) && is_numeric($secondarg)) {
            $result = $this->calculateResult($firstarg, $operation, $secondarg);
            return new JsonResponse([
                'expression' => $this->getExpressionString(
                    $firstarg,
                    $operation,
                    $secondarg
                ),
                'result' => $result,
            ]);
        }
        else
        {
            return new JsonResponse([
                'expression' => $this->getExpressionString(
                    $firstarg,
                    $operation,
                    $secondarg
                ),
                'result' => "Check the arguments",
            ]);
        }
    }
    #[Route('/save-values', name: 'save_values')]
    public function saveValues(Request $request)
    {
        $client = RedisAdapter::createConnection(
            'redis://127.0.0.1:6379'
        );
        $memcached = new RedisAdapter($client);

        $firstarg = $memcached->getItem('firstarg');
        $data = $request->get('firstarg') ;
        $firstarg->set($data);
        $memcached->save($firstarg);

        $secondarg = $memcached->getItem('secondarg');
        $data = $request->get('secondarg') ;
        $secondarg->set($data);
        $memcached->save($secondarg);


        $operation = $memcached->getItem('operation');
        $data = $request->get('operation') ;
        $operation->set($data);
        $memcached->save($operation);


        return new JsonResponse([
            'firstarg' =>$firstarg,
            'operation'=> $operation,
            'secondarg'=> $secondarg
        ]);
    }
    #[Route('/restore-values', name: 'restore_values')]
    public function restoreValues()
    {
        $client = RedisAdapter::createConnection(
            'redis://127.0.0.1:6379'
        );
        $memcached = new RedisAdapter($client);
        $firstarg = $memcached->getItem('firstarg');
        $firstarg = $firstarg->get();

        $secondarg = $memcached->getItem('secondarg');
        $secondarg = $secondarg->get();

        $operation = $memcached->getItem('operation');
        $operation = $operation->get();

            return new JsonResponse([
                'firstarg' =>$firstarg,
                    'operation'=> $operation,
                    'secondarg'=> $secondarg
            ]);
    }
    private function calculateResult($firstArgument, $operation, $secondArgument)
    {
        try {
            switch ($operation) {
                case '+':
                    return $firstArgument + $secondArgument;
                case '-':
                    return $firstArgument - $secondArgument;
                case '*':
                    return $firstArgument * $secondArgument;
                case '/':
                    if ($secondArgument != 0) {
                        return $firstArgument / $secondArgument;
                    } else {
                        //throw new \InvalidArgumentException('Division by zero');
                        return 'Division by zero';
                    }
                default:
                    return 'Invalid operation';
                //throw new \InvalidArgumentException('Invalid operation');
            }
        }catch (Exception $exception)
        {
            return  "Check the arguments";
        }

    }

    private function getExpressionString($firstArgument, $operation, $secondArgument)
    {
        return $firstArgument . ' ' . $operation. ' ' . $secondArgument;
    }
}