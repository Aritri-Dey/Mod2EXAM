<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\Validation;
use App\Entity\Stocks;
use App\Entity\UserInfoTable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * This class implements the main controller of the application
 * that handles all the functions related to routing.
 * Initailaizes entity objects and calls necessary functions to validate forms,
 * enters data in database, deletes data from databse, and displays user requested pages.
 */
class StockController extends AbstractController
{

  /**
   *  @var object $validation
   *    Stores object of Validation class.
   */
  private $validation;

  /**
   *  @var object $userInfoTable
   *    Stores object of UserInfoTable class.
   */
  private $userInfoTable;

  /**
   *  @var object $stocks
   *    Stores object of Stocks class.
   */
  private $stocks;

  /**
   *  @var object $em
   *    Global variable that stores object of EntityManagerInterface class.
   */
  private $em;

  /**
   * Constructor to initialize class variables and objects.
   * 
   *  @param object $entityManager 
   *    Stores object of EntityManager class.
   */
  public function __construct(EntityManagerInterface $entityManager) {
    $this->em = $entityManager;
    $this->userInfoTable = $this->em->getRepository(UserInfoTable::class);
    $this->validation = $this->em->getRepository(Validation::class);
    $this->stocks = $this->em->getRepository(Stocks::class);
  }

  /**
   * Function to render the index page to the user.
   * 
   *  @return Response
   *    Returns and renders index page.
   */
  #[Route('/', name: 'app_stock')]
  public function index(): Response
  {
    return $this->render('stock/index.html.twig');
  }

  /**
   * Function to render the signUp page.
   * 
   *  @return Response
   *    Returns and renders signup page.
   */
  #[Route('/signUp', name: 'signUp')]
  public function signUp(): Response
  {
    return $this->render('stock/signup.html.twig');
  }

  /**
   * Function to render the signUp page.
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @return jsonResponse
   *    Returns json data to ajax function.
   */
  #[Route('/addUser', name: 'addUser')]
  public function addUser(Request $rq): Response
  {
    $userName = $rq->request->get('userName');
    $email = $rq->request->get('email');
    $phone = $rq->request->get('phone');
    $password = $rq->request->get('password');
    // Calling Validation class functions to validate data.
    if (!$this->validation->checkEmpty($userName) || !$this->validation->validateName($userName)) {
      return $this->render('stock/signup.html.twig',[
        'err' => "Enter proper username",
      ]);
    }
    else if (!$this->validation->checkEmpty($email) || !$this->validation->checkEmail($email)) {
      return $this->render('stock/signup.html.twig',[
        'err' => "Enter valid email",
      ]);
    }
    else if (!$this->validation->checkEmpty($phone) || !$this->validation->validateNo($phone)) {
      return $this->render('stock/signup.html.twig',[
        'err' => "Enter valid phone number",
      ]);
    }
    else if (!$this->validation->checkEmpty($password)) {
      return $this->render('stock/signup.html.twig',[
        'err' => "Enter a password",
      ]);
    }
    // If all fields re valid then only data is set inn database.
    $this->userInfoTable->setName($userName);
    $this->userInfoTable->setEmail($email);
    $this->userInfoTable->setPassword($password);
    $this->userInfoTable->setPhone($phone);
    $this->em->persist($this->userInfoTable);
    $this->em->flush(); 
    return new JsonResponse("User added successfully.");
  }

  /**
   * Function to login.
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @return Response
   *    Returns and renders page according to satisfied condition.
   */
  #[Route('/login', name: 'login')]
  public function login(Request $rq): Response {
    if ($rq->get('loginBtn')) {
      // Getting input field values through Request.
      $userNameForm = $rq->get("userName");
      $emailForm = $rq->get("email");
      $passwordForm = md5($rq->get("password"));
      // Form field validation.
      if (!$this->validation->checkEmpty($userNameForm) || !$this->validation->validateName($userNameForm)) {
        return $this->render('stock/index.html.twig',[
          'err' => "Enter proper username",
        ]);
      }
      else if (!$this->validation->checkEmpty($emailForm) || !$this->validation->checkEmail($emailForm)) {
        return $this->render('stock/index.html.twig',[
          'err' => "Enter valid email",
        ]);
      }
      else if ($this->validation->checkEmpty($passwordForm)) {
        return $this->render('stock/index.html.twig',[
          'err' => $this->validation->validatePasswordEmpty($passwordForm),
          ]);
      }
      $rep = $this->userInfoTable->findOneBy(['userName' => $userNameForm]);
      if ($rep) {
        $user = $rep->getId();
        $password = $rep->getPassword();
        $email = $rep->getEmail();
        // If correct credentails are filled, user is logged in, loggedin session 
        // variable is set to 1, route is deirected to display all stocks.
        if ($password == $passwordForm && $email == $emailForm) {
          $session = $rq->getSession();
          $session->set('loggedin', 1);
          $session->set('user', $user);
          return $this->redirectToRoute('showStocks');
        }
        return $this->render('stock/index.html.twig',[
          "errMessage" => "Wrong credentials"
        ]);
      }
      return $this->render('stock/index.html.twig',[
        "errMessage" => "User does not exist"
      ]);
    }
    return $this->render('stock/index.html.twig');
  }

  /**
   * Function to show the stock market page to logged in users.
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @return Response
   *    Returns and renders stockMarket page.
   */
  #[Route('/showStocks', name: 'showStocks')]
  public function showStocks(Request $rq): Response {
    $session = $rq->getSession();
    if ($session->get('loggedin')) {
      $stocks = $this->stocks->findAll();
      return $this->render('stock/stockMarket.html.twig',[
        'stocks' => $stocks,
      ]);
    }
    return $this->render('stock/index.html.twig');
  }

  /**
   * Function to logout user.
   * When a user logs out of the application, the 'loggedin' session 
   * variable is set to 0.
   * 
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @return Response
   *    Returns the logged out page.
   */
  #[Route('/loggedOut', name: 'loggedOut')]
  public function loggedOut(Request $rq): Response {  
    $session = $rq->getSession();
    $session->set('loggedin', 0);
    return $this->render('stock/logout.html.twig',[
      'loggedin' => 0,  
    ]);
  }

  /**
 * Function to add stocks to databse and display to user.
 * 
 *  @param Request $rq
 *    Gets information from client request through form.
 * 
 *  @return Response
 *    Returns the stockMarket page.
 */
  #[Route('/addStock', name: 'addStock')]
  public function addStock(Request $rq): Response {  
    if ($rq->get('addStockBtn')) {
      $name = $rq->get("stockName");
      $price = $rq->get("stockPrice");
      // Inserting data into stocks entity.
      $this->stocks->setName($name);
      $this->stocks->setPrice($price);
      $this->stocks->createDate(date("Y-m-d"));
      $this->stocks->setLastUpdated(date("Y-m-d"));
      $rep = $this->userInfoTable->findOneBy(['userName' => $_SESSION['user']]);
      $userId = $rep->getId();
      $this->stocks->setUserId($userId);
      $this->em->persist($this->stocks);
      $this->em->flush();

      $stocks = $this->stocks->findBy(['userId' => $userId]);
      return $this->render('/stocks/stockEntry.html.twig',[
        "msg" => "Stock entered successfully.",
        "myStocks" => $stocks
      ]); 
    }
   return $this->render('/stock/stockEntry.html.twig');
  }

  /**
   * Function to return and render the stocks entry page.
   * 
   *  @return Response
 *      Returns the stockMarket page.
   */
  #[Route('/showStockForm', name: 'showStockForm')]
  public function showStockForm(Request $rq): Response {  
    return $this->render('/stocks/stockEntry.html.twig');
  } 

  /**
   * Function to return and render the stocks update page.
   * 
   *  @return Response
 *      Returns the update stock form .
   */
  #[Route('/updateForm', name: 'updateForm')]
  public function updateForm(Request $rq): Response {  
    return $this->render('/stocks/updateStock.html.twig');
  } 

  /**
 * Function to update stocks to databse through ajax if form is submitted,
 * or to display update form.
 * 
 *  @param Request $rq
 *    Gets information from client request through form.
 *  @param int $id
 *    Stofes id of stock to be updated.
 * 
 *  @return JsonResponse
 *    Returns json response.
 */
  #[Route('/updateStock', name: 'updateStock')]
  public function updateStock(Request $rq, int $id): Response { 
    if ($rq->get('updateStockBtn')) {
      return $this->render('/stock/updateStock.html.twig');
    }
    $stockName = $rq->request->get('stockName');
    $stockPrice = $rq->request->get('stockPrice');
    if (!$this->validation->checkEmpty($stockName) || !$this->validation->checkEmpty($stockPrice)) {
      return $this->render('/stock/updateStock.html.twig',[
        "err" => "Please fill all fields"
      ]);
    }
    $rep = $this->stocks->findBy(['id' => $id, 'userId' => $_SESSION['user']]);
    $rep->setName($stockName); 
    $rep->setPrice($stockPrice);
    $this->em->persist($rep);
    $this->em->flush();
    return new JsonResponse("Updated stocks successfully.");
   }

   /**
   * Function to delete an entry from database.
   *  
   *  @param Request $rq
   *    Gets information from client request through form.
   * 
   *  @return jsonResponse
   *    Redirects route to function to show favourites page after deleting. 
   */
  #[Route('/deleteStock', name: 'deleteStock')]
  public function deleteFav(Request $rq): Response { 
    $userId = $rq->request->get('userId');
    $stockId = $rq->request->get('stockId');
    $rep = $this->stocks->findBy(['userId' => $userId, 'id' => $stockId]);
    if ($rep) {
      $this->em->remove($rep);
      $this->em->flush();
      return new JsonResponse("Deleted");
    }
    return new JsonResponse("There was a problem deleting the entry.");
  }
}
