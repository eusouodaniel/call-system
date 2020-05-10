<?php

namespace AppBundle\Controller\Backend;

use Lunetics\LocaleBundle\Event\FilterLocaleSwitchEvent;
use Lunetics\LocaleBundle\LocaleBundleEvents;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use AppBundle\Controller\BaseController;
use AppBundle\Form\ChamadoSearchType;
use AppBundle\Entity\ChamadoSearch;
use AppBundle\Entity\Attendance;
use AppBundle\Service\XLSHelper;

/**
 * Report controller.
 *
 * @Route("/backend/report")
 */
class ReportController extends BaseController {

    /**
     * Relatório de chamados por cliente.
     * @Route("/chamados-cliente", name="backend_report_calls_client")
     * @Template()
     */
    public function callsClientAction(Request $request) {
      $chamadoSearch = new ChamadoSearch();
      $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);
      if ($request->getMethod() == 'POST') {
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('AppBundle:Client')->findChamadoByClient($chamadoSearch);
      }else{
        $entities = array();
      }
      return array(
        'entities' => $entities,
        'form'=> $form->createView()
      );
    }

    /**
     * Relatório de chamados por cliente.
     * @Route("/chamados-cliente-export", name="backend_report_calls_client_export")
     * @Template()
     */
    public function callsClientExportAction(Request $request) {
      // ask the service for a Excel5
      $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

      $phpExcelObject->getProperties()->setCreator("Sistema de chamados")
      ->setTitle("Chamados por clientes");
      $phpExcelObject->setActiveSheetIndex(0)
      ->setCellValue('A1', 'Nome')
      ->setCellValue('B1', 'Total de chamados');
      $phpExcelObject->getActiveSheet()->setTitle('Chamados por clientes');
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $phpExcelObject->setActiveSheetIndex(0);

      $chamadoSearch = new ChamadoSearch();
      $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);
      $form->handleRequest($request);

      $em = $this->getDoctrine()->getManager();
      $entities = $em->getRepository('AppBundle:Client')->findChamadoByClient($chamadoSearch);
      $row = 2;
      // Iteração sobre os tickets do evento
      foreach ($entities as $entity) {
          // Header da listagem de tickets
        $phpExcelObject->getActiveSheet()
        ->setCellValue('A' . $row, $entity->getName()." - ".$entity->getEmail())
        ->setCellValue('B' . $row, $entity->getTotalchamados());
        $row++;
      }

      //Autosize nas colunas da planilha
      foreach (range('A','B') as $columnID) {
        $phpExcelObject->getActiveSheet()->getColumnDimension($columnID)
        ->setAutoSize(true);
      }

      // create the writer
      $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
      // create the response
      $response = $this->get('phpexcel')->createStreamedResponse($writer);
      // adding headers
      $dispositionHeader = $response->headers->makeDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        'chamados-por-clientes.xls'
      );
      $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
      $response->headers->set('Pragma', 'public');
      $response->headers->set('Cache-Control', 'maxage=1');
      $response->headers->set('Content-Disposition', $dispositionHeader);

      return $response;
    }

    /**
     * Relatório de chamados por cliente.
     * @Route("/base-cliente", name="backend_report_base_client")
     * @Template()
     */
    public function baseClientsAction(Request $request) {
      $chamadoSearch = new ChamadoSearch();
      $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);
      if ($request->getMethod() == 'POST') {
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('AppBundle:Company')->findBySearch($chamadoSearch);
      }else{
        $entities = array();
      }
      return array(
        'entities' => $entities,
        'form'=> $form->createView()
      );
    }

    /**
     * Relatório de chamados por cliente.
     * @Route("/base-cliente-export", name="backend_report_base_client_export")
     * @Template()
     */
    public function baseClientExportAction(Request $request) {
      // ask the service for a Excel5
      $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

      $phpExcelObject->getProperties()->setCreator("Sistema de chamados")
      ->setTitle("Base de clientes");
      $phpExcelObject->setActiveSheetIndex(0)
      ->setCellValue('A1', 'Id')
      ->setCellValue('B1', 'Nome')
      ->setCellValue('C1', 'Tipo')
      ->setCellValue('D1', 'Tipo de Contrato')
      ->setCellValue('E1', 'Email')
      ->setCellValue('F1', 'Telefone');
      $phpExcelObject->getActiveSheet()->setTitle('Base de clientes');
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $phpExcelObject->setActiveSheetIndex(0);

      $chamadoSearch = new ChamadoSearch();
      $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);
      $form->handleRequest($request);

      $em = $this->getDoctrine()->getManager();
      $entities = $em->getRepository('AppBundle:Company')->findBySearch($chamadoSearch);
      $row = 2;
      // Iteração sobre os tickets do evento
      foreach ($entities as $entity) {
          // Header da listagem de tickets
        $phpExcelObject->getActiveSheet()
        ->setCellValue('A' . $row, $entity->getId())
        ->setCellValue('B' . $row, $entity->getName())
        ->setCellValue('C' . $row, $entity->getType()=="PJ"?"Pessoa Jurídica":"Pessoa Física")
        ->setCellValue('D' . $row, $entity->getContract()=="Contrato"?"Contrato":"Avulso")
        ->setCellValue('E' . $row, $entity->getEmail())
        ->setCellValue('F' . $row, $entity->getTelphone());
        $row++;
      }

      //Autosize nas colunas da planilha
      foreach (range('A','F') as $columnID) {
        $phpExcelObject->getActiveSheet()->getColumnDimension($columnID)
        ->setAutoSize(true);
      }

      // create the writer
      $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
      // create the response
      $response = $this->get('phpexcel')->createStreamedResponse($writer);
      // adding headers
      $dispositionHeader = $response->headers->makeDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        'base-de-clientes.xls'
      );
      $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
      $response->headers->set('Pragma', 'public');
      $response->headers->set('Cache-Control', 'maxage=1');
      $response->headers->set('Content-Disposition', $dispositionHeader);

      return $response;
    }

    /**
     * Relatório de porcentagem de SLA por tipo de atendimento.
     * @Route("/porcentagem-sla-tipoatendimento", name="backend_report_percentage_sla")
     * @Template()
     */
    public function percentageSLAAction(Request $request) {
      $chamadoSearch = new ChamadoSearch();
      $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);
      $form->handleRequest($request);
      $em = $this->getDoctrine()->getManager();
      $entities = array();
      $itemQuantity = 0;
      $itemQuantityAttended = 0;
      $attendanceItem = "";
      $chamados = $em->getRepository('AppBundle:Chamado')->findAttendanceAverageTime($chamadoSearch);
      if(count($chamados) > 0){
        $attendanceItem = $chamados[0]->getAttendance();
      }
      foreach ($chamados as $chamado) {
        if($attendanceItem->getId()!=$chamado->getAttendance()->getId()){
          $percent = ($itemQuantityAttended*100)/$itemQuantity;
          $attendanceItem->setPercentAttended($percent."%");
          array_push($entities,$attendanceItem);
          $itemQuantity = 0;
          $itemQuantityAttended = 0;
          $attendanceItem = $chamado->getAttendance();
        }
        $tempoAtendimento = $chamado->getDtEnd()->diff($chamado->getDtCreation());
        $sla = $em->getRepository('AppBundle:SLA')->findOneByItemAttendance($chamado->getItem(),$chamado->getAttendance());
        $tempoAtendimento = ((($tempoAtendimento->days * 24) + $tempoAtendimento->h)*60 + $tempoAtendimento->i)*60 + $tempoAtendimento->s;
        if($sla != null){
          $slaHour = ($sla->getHour()*60)*60;
          if($tempoAtendimento <= $slaHour){
            $itemQuantityAttended++;
          }
        }
        $itemQuantity++;
      }
      if($itemQuantity > 0){
        $percent = ($itemQuantityAttended*100)/$itemQuantity;
        $attendanceItem->setPercentAttended($percent."%");
        array_push($entities,$attendanceItem);
      }

      return array(
        'entities' => $entities,
        'form'=> $form->createView()
      );
    }

    /**
    * Relatório de porcentagem de SLA por tipo de atendimento.
    * @Route("/porcentagem-sla-tipoatendimento-export", name="backend_report_percentage_sla-export")
    * @Template()
    */
    public function percentageSLAExportAction(Request $request) {
      // ask the service for a Excel5
      $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

      $phpExcelObject->getProperties()->setCreator("Sistema de chamados")
      ->setTitle("% SLA por tipo de atendimento");
      $phpExcelObject->setActiveSheetIndex(0)
      ->setCellValue('A1', 'Tipo')
      ->setCellValue('B1', 'Percentual de SLA Atendido');
      $phpExcelObject->getActiveSheet()->setTitle('% SLA por tipo de atendimento');
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $phpExcelObject->setActiveSheetIndex(0);

      $chamadoSearch = new ChamadoSearch();
      $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);
      $form->handleRequest($request);

      $em = $this->getDoctrine()->getManager();
      $chamadoSearch = new ChamadoSearch();
      $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);
      $form->handleRequest($request);
      $em = $this->getDoctrine()->getManager();
      $entities = array();
      $itemQuantity = 0;
      $itemQuantityAttended = 0;
      $attendanceItem = "";
      $chamados = $em->getRepository('AppBundle:Chamado')->findAttendanceAverageTime($chamadoSearch);
      if(count($chamados) > 0){
        $attendanceItem = $chamados[0]->getAttendance();
      }
      foreach ($chamados as $chamado) {
        if($attendanceItem->getId()!=$chamado->getAttendance()->getId()){
          $percent = ($itemQuantityAttended*100)/$itemQuantity;
          $attendanceItem->setPercentAttended($percent."%");
          array_push($entities,$attendanceItem);
          $itemQuantity = 0;
          $itemQuantityAttended = 0;
          $attendanceItem = $chamado->getAttendance();
        }
        $tempoAtendimento = $chamado->getDtEnd()->diff($chamado->getDtCreation());
        $sla = $em->getRepository('AppBundle:SLA')->findOneByItemAttendance($chamado->getItem(),$chamado->getAttendance());
        $tempoAtendimento = ((($tempoAtendimento->days * 24) + $tempoAtendimento->h)*60 + $tempoAtendimento->i)*60 + $tempoAtendimento->s;
        if($sla != null){
          $slaHour = ($sla->getHour()*60)*60;
          if($tempoAtendimento <= $slaHour){
            $itemQuantityAttended++;
          }
        }
        $itemQuantity++;
      }
      if($itemQuantity > 0){
        $percent = ($itemQuantityAttended*100)/$itemQuantity;
        $attendanceItem->setPercentAttended($percent."%");
        array_push($entities,$attendanceItem);
      }
      // Váriavel com a primeira linha dos tickets
      $row = 2;
      // Iteração sobre os tickets do evento
      foreach ($entities as $entity) {
          // Header da listagem de tickets
        $phpExcelObject->getActiveSheet()
        ->setCellValue('A' . $row, $entity->getName())
        ->setCellValue('B' . $row, $entity->getPercentAttended());
        $row++;
      }

      //Autosize nas colunas da planilha
      foreach (range('A','B') as $columnID) {
        $phpExcelObject->getActiveSheet()->getColumnDimension($columnID)
        ->setAutoSize(true);
      }

      // create the writer
      $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
      // create the response
      $response = $this->get('phpexcel')->createStreamedResponse($writer);
      // adding headers
      $dispositionHeader = $response->headers->makeDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        'sla-tipo.xls'
      );
      $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
      $response->headers->set('Pragma', 'public');
      $response->headers->set('Cache-Control', 'maxage=1');
      $response->headers->set('Content-Disposition', $dispositionHeader);

      return $response;
    }

    /**
     * Relatório de chamados pendentes por atendimento.
     * @Route("/chamados-pendentes-atendimento", name="backend_pending_chamados")
     * @Template()
     */
    public function pendingChamadosAction(Request $request) {
      $em = $this->getDoctrine()->getManager();
      $chamadoSearch = new ChamadoSearch();
      $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);
      $form->handleRequest($request);
      $entities = $em->getRepository('AppBundle:Chamado')->findByChamadoSearch($chamadoSearch, true);
      return array(
        'entities' => $entities,
        'form'=> $form->createView()
      );
    }

    /**
     * Relatório de chamados pendentes por atendimento.
     * @Route("/chamados-pendentes-atendimento-export", name="backend_pending_chamados_export")
     * @Template()
     */
    public function pendingChamadosExportAction(Request $request) {
      // ask the service for a Excel5
      $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

      $phpExcelObject->getProperties()->setCreator("Sistema de chamados")
      ->setTitle("Chamados pendentes");
      $phpExcelObject->setActiveSheetIndex(0)
      ->setCellValue('A1', 'Descrição')
      ->setCellValue('B1', 'Telefone')
      ->setCellValue('C1', 'Tipo de Atendimento')
      ->setCellValue('D1', 'Item')
      ->setCellValue('E1', 'Cliente')
      ->setCellValue('F1', 'Responsável')
      ->setCellValue('G1', 'Status');
      $phpExcelObject->getActiveSheet()->setTitle('Chamados pendentes');
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $phpExcelObject->setActiveSheetIndex(0);

      $em = $this->getDoctrine()->getManager();
      $chamadoSearch = new ChamadoSearch();
      $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);
      $form->handleRequest($request);
      $entities = $em->getRepository('AppBundle:Chamado')->findByChamadoSearch($chamadoSearch, true);
      $row = 2;
      // Iteração sobre os tickets do evento
      foreach ($entities as $entity) {
          // Header da listagem de tickets
        $phpExcelObject->getActiveSheet()
        ->setCellValue('A' . $row, $entity->getDescription())
        ->setCellValue('B' . $row, $entity->getTelphone())
        ->setCellValue('C' . $row, $entity->getAttendance())
        ->setCellValue('D' . $row, $entity->getItem())
        ->setCellValue('E' . $row, $entity->getClient()->getName()." - ". $entity->getClient()->getEmail())
        ->setCellValue('F' . $row, $entity->getUser()!=null ? $entity->getUser()->getName() : "")
        ->setCellValue('G' . $row, $entity->getStatus());
        $row++;
      }

      //Autosize nas colunas da planilha
      foreach (range('A','G') as $columnID) {
        $phpExcelObject->getActiveSheet()->getColumnDimension($columnID)
        ->setAutoSize(true);
      }

      // create the writer
      $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
      // create the response
      $response = $this->get('phpexcel')->createStreamedResponse($writer);
      // adding headers
      $dispositionHeader = $response->headers->makeDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        'chamados-pendentes.xls'
      );
      $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
      $response->headers->set('Pragma', 'public');
      $response->headers->set('Cache-Control', 'maxage=1');
      $response->headers->set('Content-Disposition', $dispositionHeader);

      return $response;
    }


    /**
     * Relatório de tabela geral.
     * @Route("/tabela-geral", name="backend_general_table")
     * @Template()
     */
    public function generalTableAction(Request $request) {
      $em = $this->getDoctrine()->getManager();
      $chamadoSearch = new ChamadoSearch();
      $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);
      if ($request->getMethod() == 'POST') {
        $form->handleRequest($request);
        $entities = $em->getRepository('AppBundle:Chamado')->findByChamadoSearch($chamadoSearch);
        foreach ($entities as $entity) {
          if($entity->getDtEnd()){
            $tempoAtendimento = $entity->getDtEnd()->diff($entity->getDtCreation());
            $sla = $em->getRepository('AppBundle:SLA')->findOneByItemAttendance($entity->getItem(),$entity->getAttendance());
            $tempoAtendimentoHoras = ($tempoAtendimento->days * 24) + $tempoAtendimento->h;
            $tempoAtendimentoMinutos = $tempoAtendimento->i < 10 ? "0".$tempoAtendimento->i : $tempoAtendimento->i;
            $tempoAtendimentoSegundos = $tempoAtendimento->s < 10 ? "0".$tempoAtendimento->s : $tempoAtendimento->s;
            $tempoAtendimento = ((($tempoAtendimento->days * 24) + $tempoAtendimento->h)*60 + $tempoAtendimento->i)*60 + $tempoAtendimento->s;
            if($sla!=null){
              $slaHour = ($sla->getHour()*60)*60;
              if($tempoAtendimento > $slaHour){
                $entity->setSlaAtingido("Não");
              }else{
                $entity->setSlaAtingido("Sim");
              }
              $entity->setSla($sla->getHour().":00");
            }else{
              $entity->setSlaAtingido("Sim");
              $entity->setSla("00:00");
            }

            $entity->setTempoAtendimento($tempoAtendimentoHoras.":".$tempoAtendimentoMinutos.":".$tempoAtendimentoSegundos);
          }else{
            $entity->setSla("-");
            $entity->setSlaAtingido("-");
            $entity->setTempoAtendimento("-");
          }
        }
      }else{
        $entities = array();
      }
      return array(
        'entities' => $entities,
        'form'=> $form->createView()
      );
    }

    /**
     * Relatório de tabela geral.
     * @Route("/tabela-geral-export", name="backend_general_table_export")
     * @Template()
     */
    public function generalTableExportAction(Request $request) {
      $em = $this->getDoctrine()->getManager();
      $chamadoSearch = new ChamadoSearch();
      $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);
      $form->handleRequest($request);
      $entities = $em->getRepository('AppBundle:Chamado')->findByChamadoSearch($chamadoSearch);
      foreach ($entities as $entity) {
        if($entity->getDtEnd()){
          $tempoAtendimento = $entity->getDtEnd()->diff($entity->getDtCreation());
          $sla = $em->getRepository('AppBundle:SLA')->findOneByItemAttendance($entity->getItem(),$entity->getAttendance());
          $tempoAtendimento = ((($tempoAtendimento->days * 24) + $tempoAtendimento->h)*60 + $tempoAtendimento->i)*60 + $tempoAtendimento->s;
          if($sla!=null){
            $slaHour = ($sla->getHour()*60)*60;
            if($tempoAtendimento > $slaHour){
              $entity->setSlaAtingido("Não");
            }else{
              $entity->setSlaAtingido("Sim");
            }
            $entity->setSla($sla->getHour().":00");
          }else{
            $entity->setSlaAtingido("Sim");
            $entity->setSla("00:00");
          }

          $entity->setTempoAtendimento(gmdate("H:i:s", $tempoAtendimento));
        }else{
          $entity->setSla("-");
          $entity->setSlaAtingido("-");
          $entity->setTempoAtendimento("-");
        }
      }
      // ask the service for a Excel5
      $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

      $phpExcelObject->getProperties()->setCreator("Sistema de chamados")
      ->setTitle("Tabela Geral");
      $phpExcelObject->setActiveSheetIndex(0)
      ->setCellValue('A1', 'Número do Chamado')
      ->setCellValue('B1', 'Empresa')
      ->setCellValue('C1', 'Aberto por')
      ->setCellValue('D1', 'Status')
      ->setCellValue('E1', 'Data abertura')
      ->setCellValue('F1', 'Descrição do chamado')
      ->setCellValue('G1', 'Data Fechamento')
      ->setCellValue('H1', 'Tratado por')
      ->setCellValue('I1', 'Tipo de Atendimento')
      ->setCellValue('J1', 'Item')
      ->setCellValue('K1', 'Prazo para Atendimento')
      ->setCellValue('L1', 'SLA Atingido')
      ->setCellValue('M1', 'Descrição Fechamento')
      ->setCellValue('N1', 'Responsável')
      ;
      $phpExcelObject->getActiveSheet()->setTitle("Tabela Geral");
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $phpExcelObject->setActiveSheetIndex(0);

      $row = 2;
      // Iteração sobre os tickets do evento
      foreach ($entities as $entity) {
        $empresa = $entity->getEnterprise()!=null ? $entity->getEnterprise() : null;
        if($empresa == null){
         $empresa = $entity->getClient() != null ? $entity->getClient()->getCompany() : "";
       }

       $client = $entity->getClient() != null ? $entity->getClient()->getName()." - ". $entity->getClient()->getEmail() : "";

          // Header da listagem de tickets
       $phpExcelObject->getActiveSheet()
       ->setCellValue('A' . $row, $entity->getId())
       ->setCellValue('B' . $row, $empresa)
       ->setCellValue('C' . $row, $client)
       ->setCellValue('D' . $row, $entity->getStatus())
       ->setCellValue('E' . $row, $entity->getDtCreation()->format("d/m/Y"))
       ->setCellValue('F' . $row, $entity->getDescription())
       ->setCellValue('G' . $row, $entity->getDtEnd()!=null ? $entity->getDtEnd()->format("d/m/Y") : "-")
       ->setCellValue('H' . $row, $entity->getUser()!=null ? $entity->getUser()->getName() : "-")
       ->setCellValue('I' . $row, $entity->getAttendance())
       ->setCellValue('J' . $row, $entity->getItem())
       ->setCellValue('K' . $row, $entity->getDtLimit()!=null ? $entity->getDtLimit()->format("d/m/Y") : "-")
       ->setCellValue('L' . $row, $entity->getSlaAtingido())
       ->setCellValue('M' . $row, $entity->getConclusionEnd())
       ->setCellValue('N' . $row, $entity->getResponsible()!=null ? $entity->getResponsible() : "-")
       ;
       $row++;
     }

      //Autosize nas colunas da planilha
     foreach (range('A','O') as $columnID) {
      $phpExcelObject->getActiveSheet()->getColumnDimension($columnID)
      ->setAutoSize(true);
    }

      // create the writer
    $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
      // create the response
    $response = $this->get('phpexcel')->createStreamedResponse($writer);
      // adding headers
    $dispositionHeader = $response->headers->makeDisposition(
      ResponseHeaderBag::DISPOSITION_ATTACHMENT,
      'tabela-geral.xls'
    );
    $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
    $response->headers->set('Pragma', 'public');
    $response->headers->set('Cache-Control', 'maxage=1');
    $response->headers->set('Content-Disposition', $dispositionHeader);

    return $response;
  }

    /**
     * Relatório de tempo médio de atendimento por tipo de atendimento
     * @Route("/tempo-medio-tipoatendimento", name="backend_report_average_time")
     * @Template()
     */
    public function averageTimeAction(Request $request) {
      $chamadoSearch = new ChamadoSearch();
      $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);
      $form->handleRequest($request);
      $em = $this->getDoctrine()->getManager();
      $entities = array();
      $timeSeconds = 0;
      $itemQuantity = 0;
      $attendanceItem = "";
      $chamados = $em->getRepository('AppBundle:Chamado')->findAttendanceAverageTime($chamadoSearch);
      if(count($chamados) > 0){
        $attendanceItem = $chamados[0]->getAttendance();
      }
      foreach ($chamados as $chamado) {
        if($attendanceItem->getId()!=$chamado->getAttendance()->getId()){
          $meanTime = $timeSeconds/$itemQuantity;
          $attendanceItem->setAveragetime(gmdate("H:i:s", $meanTime));
          array_push($entities,$attendanceItem);
          $timeSeconds = 0;
          $itemQuantity = 0;
          $attendanceItem = $chamado->getAttendance();
        }
        $tempoAtendimento = $chamado->getDtEnd()->diff($chamado->getDtCreation());
        $tempoAtendimento = ((($tempoAtendimento->days * 24) + $tempoAtendimento->h)*60 + $tempoAtendimento->i)*60 + $tempoAtendimento->s;
        $timeSeconds+=$tempoAtendimento;
        $itemQuantity++;
      }
      if($itemQuantity > 0){
        $meanTime = $timeSeconds/$itemQuantity;
        $attendanceItem->setAveragetime(gmdate("H:i:s", $meanTime));
        array_push($entities,$attendanceItem);
      }

      return array(
        'entities' => $entities,
        'form'=> $form->createView()
      );
    }

    /**
     * Relatório de tempo médio de atendimento por tipo de atendimento
     * @Route("/tempo-medio-tipoatendimento-export", name="backend_report_average_time_export")
     * @Template()
     */
    public function averageTimeExportAction(Request $request) {
      $chamadoSearch = new ChamadoSearch();
      $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);
      $form->handleRequest($request);
      $em = $this->getDoctrine()->getManager();
      $entities = array();
      $timeSeconds = 0;
      $itemQuantity = 0;
      $attendanceItem = "";
      $chamados = $em->getRepository('AppBundle:Chamado')->findAttendanceAverageTime($chamadoSearch);
      if(count($chamados) > 0){
        $attendanceItem = $chamados[0]->getAttendance();
      }
      foreach ($chamados as $chamado) {
        if($attendanceItem->getId()!=$chamado->getAttendance()->getId()){
          $meanTime = $timeSeconds/$itemQuantity;
          $attendanceItem->setAveragetime(gmdate("H:i:s", $meanTime));
          array_push($entities,$attendanceItem);
          $timeSeconds = 0;
          $itemQuantity = 0;
          $attendanceItem = $chamado->getAttendance();
        }
        $tempoAtendimento = $chamado->getDtEnd()->diff($chamado->getDtCreation());
        $tempoAtendimento = ((($tempoAtendimento->days * 24) + $tempoAtendimento->h)*60 + $tempoAtendimento->i)*60 + $tempoAtendimento->s;
        $timeSeconds+=$tempoAtendimento;
        $itemQuantity++;
      }
      if($itemQuantity > 0){
        $meanTime = $timeSeconds/$itemQuantity;
        $attendanceItem->setAveragetime(gmdate("H:i:s", $meanTime));
        array_push($entities,$attendanceItem);
      }

      // ask the service for a Excel5
      $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

      $phpExcelObject->getProperties()->setCreator("Sistema de chamados")
      ->setTitle("Tempo médio");
      $phpExcelObject->setActiveSheetIndex(0)
      ->setCellValue('A1', 'Tipo')
      ->setCellValue('B1', 'Média');
      $phpExcelObject->getActiveSheet()->setTitle('Tempo médio');
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $phpExcelObject->setActiveSheetIndex(0);

      $row = 2;
      // Iteração sobre os tickets do evento
      foreach ($entities as $entity) {
          // Header da listagem de tickets
        $phpExcelObject->getActiveSheet()
        ->setCellValue('A' . $row, $entity->getName())
        ->setCellValue('B' . $row, $entity->getAveragetime());
        $row++;
      }

      //Autosize nas colunas da planilha
      foreach (range('A','B') as $columnID) {
        $phpExcelObject->getActiveSheet()->getColumnDimension($columnID)
        ->setAutoSize(true);
      }

      // create the writer
      $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
      // create the response
      $response = $this->get('phpexcel')->createStreamedResponse($writer);
      // adding headers
      $dispositionHeader = $response->headers->makeDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        'tempo-medio.xls'
      );
      $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
      $response->headers->set('Pragma', 'public');
      $response->headers->set('Cache-Control', 'maxage=1');
      $response->headers->set('Content-Disposition', $dispositionHeader);

      return $response;
    }

    /**
     * Relatório de status por tipo de atendimento.
     * @Route("/status-tipo-atendimento", name="backend_report_status_attendance")
     * @Template()
     */
    public function statusAttendanceAction(Request $request) {
      //Total de chamados pendentes
      $totalPendent = 0;
      //Total de chamados concluídos
      $totalConcluded = 0;
      //Total de chamados fora do prazo
      $totalFP = 0;
      //Total de chamados concluídos no prazo
      $totalConcludedOnTime = 0;
      //Total de chamados concluídos no prazo em percentual
      $totalConcludedOnTimePercent = 0;

      if ($request->getMethod() == 'POST') {
        $em = $this->getDoctrine()->getManager();
        //Busca a lista de atendimentos
        $attendanceList = $em->getRepository('AppBundle:Attendance')->findAll();

        $chamadoSearch = new ChamadoSearch();
        $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);
        $form->handleRequest($request);

        foreach ($attendanceList as $attendance) {
          $this->processStatusAttendanceTable($attendance, $chamadoSearch);
          $totalPendent+=$attendance->getStatusPendent();
          $totalConcluded+=$attendance->getStatusConcluded();
          $totalFP+=$attendance->getStatusOuttatime();
          $totalConcludedOnTime+=$attendance->getStatusConcludedOnTime();
        }
        if($totalConcluded == 0){
          $totalConcludedOnTimePercent = 0;
        }else{
          $totalConcludedOnTimePercent = ($totalConcludedOnTime*100)/$totalConcluded;
        }
        $entities = $attendanceList;
      }else{
        //Obtém o primeiro dia do mês
        $begin_date = new \DateTime('first day of this month');
        //Obtém o último dia do mês
        $last_date = new \DateTime('last day of this month');

        //Seta os valores padrões no objeto
        $chamadoSearch = new ChamadoSearch();
        $chamadoSearch->setBeginDate($begin_date);
        $chamadoSearch->setEndDate($last_date);

        $em = $this->getDoctrine()->getManager();
        //Busca a lista de atendimentos
        $attendanceList = $em->getRepository('AppBundle:Attendance')->findAll();
        $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);
        foreach ($attendanceList as $attendance) {
          $this->processStatusAttendanceTable($attendance, $chamadoSearch);
          $totalPendent+=$attendance->getStatusPendent();
          $totalConcluded+=$attendance->getStatusConcluded();
          $totalFP+=$attendance->getStatusOuttatime();
          $totalConcludedOnTime+=$attendance->getStatusConcludedOnTime();
        }
        if($totalConcluded == 0){
          $totalConcludedOnTimePercent = 0;
        }else{
          $totalConcludedOnTimePercent = ($totalConcludedOnTime*100)/$totalConcluded;
        }
        $entities = $attendanceList;

      }
      return array(
        'entities' => $entities,
        'totalConcluded' => $totalConcluded,
        'totalFP' => $totalFP,
        'totalPendent' => $totalPendent,
        'totalConcludedOnTime' => $totalConcludedOnTime,
        'totalConcludedOnTimePercent' => $totalConcludedOnTimePercent,
        'form'=> $form->createView()
      );
    }

    /**
     * Relatório de status por tipo de atendimento.
     * @Route("/status-tipo-atendimento-export", name="backend_report_status_attendance_export")
     * @Template()
     */
    public function statusAttendanceExportAction(Request $request) {
      //Total de chamados pendentes
      $totalPendent = 0;
      //Total de chamados concluídos
      $totalConcluded = 0;
      //Total de chamados fora do prazo
      $totalFP = 0;
      //Total de chamados concluídos no prazo
      $totalConcludedOnTime = 0;
      //Total de chamados concluídos no prazo em percentual
      $totalConcludedOnTimePercent = 0;

      if ($request->getMethod() == 'POST') {
        $em = $this->getDoctrine()->getManager();
        //Busca a lista de atendimentos
        $attendanceList = $em->getRepository('AppBundle:Attendance')->findAll();

        $chamadoSearch = new ChamadoSearch();
        $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);
        $form->handleRequest($request);

        foreach ($attendanceList as $attendance) {
          $this->processStatusAttendanceTable($attendance, $chamadoSearch);
          $totalPendent+=$attendance->getStatusPendent();
          $totalConcluded+=$attendance->getStatusConcluded();
          $totalFP+=$attendance->getStatusOuttatime();
          $totalConcludedOnTime+=$attendance->getStatusConcludedOnTime();
        }
        if($totalConcluded == 0){
          $totalConcludedOnTimePercent = 0;
        }else{
          $totalConcludedOnTimePercent = ($totalConcludedOnTime*100)/$totalConcluded;
        }
        $entities = $attendanceList;
      }else{
        //Obtém o primeiro dia do mês
        $begin_date = new \DateTime('first day of this month');
        //Obtém o último dia do mês
        $last_date = new \DateTime('last day of this month');

        //Seta os valores padrões no objeto
        $chamadoSearch = new ChamadoSearch();
        $chamadoSearch->setBeginDate($begin_date);
        $chamadoSearch->setEndDate($last_date);

        $em = $this->getDoctrine()->getManager();
        //Busca a lista de atendimentos
        $attendanceList = $em->getRepository('AppBundle:Attendance')->findAll();
        $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);
        foreach ($attendanceList as $attendance) {
          $this->processStatusAttendanceTable($attendance, $chamadoSearch);
          $totalPendent+=$attendance->getStatusPendent();
          $totalConcluded+=$attendance->getStatusConcluded();
          $totalFP+=$attendance->getStatusOuttatime();
          $totalConcludedOnTime+=$attendance->getStatusConcludedOnTime();
        }
        if($totalConcluded == 0){
          $totalConcludedOnTimePercent = 0;
        }else{
          $totalConcludedOnTimePercent = ($totalConcludedOnTime*100)/$totalConcluded;
        }
        $entities = $attendanceList;

      }
      /*return array(
        'entities' => $entities,
        'totalConcluded' => $totalConcluded,
        'totalFP' => $totalFP,
        'totalPendent' => $totalPendent,
        'totalConcludedOnTime' => $totalConcludedOnTime,
        'totalConcludedOnTimePercent' => $totalConcludedOnTimePercent,
        'form'=> $form->createView()
      );*/

      // ask the service for a Excel5
      $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

      $phpExcelObject->getProperties()->setCreator("Sistema de chamados")
      ->setTitle("Status por tipo de atendimento");
      $phpExcelObject->setActiveSheetIndex(0)
      ->setCellValue('A1', 'Tipo de Atendimento')
      ->setCellValue('B1', 'Total Pendente')
      ->setCellValue('C1', 'Total FP')
      ->setCellValue('D1', 'Total Concluído')
      ->setCellValue('E1', '% Concluído no prazo');
      $phpExcelObject->getActiveSheet()->setTitle('Status por tipo de atendimento');
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $phpExcelObject->setActiveSheetIndex(0);

      $row = 2;
      foreach ($entities as $entity) {
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue("A".$row, $entity->getName());
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue("B".$row, $entity->getStatusPendent());
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue("C".$row, $entity->getStatusOuttatime());
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue("D".$row, $entity->getStatusConcluded());
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue("E".$row, $entity->getStatusConcludedOnTimePercent());
        $row++;
      }

      //Total Geral
      $phpExcelObject->setActiveSheetIndex(0)->setCellValue("A".$row, "Total Geral");
      $phpExcelObject->setActiveSheetIndex(0)->setCellValue("B".$row, $totalPendent);
      $phpExcelObject->setActiveSheetIndex(0)->setCellValue("C".$row, $totalFP);
      $phpExcelObject->setActiveSheetIndex(0)->setCellValue("D".$row, $totalConcluded);
      $phpExcelObject->setActiveSheetIndex(0)->setCellValue("E".$row, $totalConcludedOnTimePercent);

      //Autosize nas colunas da planilha
      foreach (range('A','E') as $columnID) {
        $phpExcelObject->getActiveSheet()->getColumnDimension($columnID)
        ->setAutoSize(true);
      }

      // create the writer
      $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
      // create the response
      $response = $this->get('phpexcel')->createStreamedResponse($writer);
      // adding headers
      $dispositionHeader = $response->headers->makeDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        'status-tipo-atendimento.xls'
      );
      $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
      $response->headers->set('Pragma', 'public');
      $response->headers->set('Cache-Control', 'maxage=1');
      $response->headers->set('Content-Disposition', $dispositionHeader);

      return $response;
    }

    protected function processStatusAttendanceTable($attendance, $chamadoSearch){
      $em = $this->getDoctrine()->getManager();

      $countFP = 0;
      $countPrazo = 0;
      //Verifica o total de chamados pendentes
      $countPendent = $em->getRepository('AppBundle:Chamado')->countChamadosByAttendance($attendance, $chamadoSearch, true);
      $attendance->setStatusPendent($countPendent);

      $chamadosPendentes = $em->getRepository('AppBundle:Chamado')->findChamadosByAttendance($attendance, $chamadoSearch, true);
      foreach ($chamadosPendentes as $chamado) {
        if($chamado->getDtEnd() == null){
          $now = new \DateTime('now');
          $chamado->setDtEnd($now);
          $tempoAtendimento = $chamado->getDtEnd()->diff($chamado->getDtCreation());
        }else{
          $tempoAtendimento = $chamado->getDtEnd()->diff($chamado->getDtCreation());
        }
        $tempoAtendimento = ($tempoAtendimento->days * 24) + $tempoAtendimento->h;
        $sla = $em->getRepository('AppBundle:SLA')->findOneByItemAttendance($chamado->getItem(), $chamado->getAttendance());
        if($tempoAtendimento > $sla->getHour()){
          $countFP++;
        }
      }
      $attendance->setStatusOuttatime($countFP);

      //Verifica o total de chamados concluídos
      $countConcluded = $em->getRepository('AppBundle:Chamado')->countChamadosByAttendance($attendance, $chamadoSearch, false, true);
      $attendance->setStatusConcluded($countConcluded);

      $chamadosConcluidos = $em->getRepository('AppBundle:Chamado')->findChamadosByAttendance($attendance, $chamadoSearch, false, true);
      foreach ($chamadosConcluidos as $chamado) {
        if($chamado->getDtEnd()!=null){
          $tempoAtendimento = $chamado->getDtEnd()->diff($chamado->getDtCreation());
        }else{
          $date = new \DateTime('now');
          $tempoAtendimento = $date->diff($chamado->getDtCreation());
        }
        $tempoAtendimento = ($tempoAtendimento->days * 24) + $tempoAtendimento->h;
        $this->log("!!!!!!!!!!! ".$chamado->getId());
        $sla = $em->getRepository('AppBundle:SLA')->findOneByItemAttendance($chamado->getItem(), $chamado->getAttendance());
        if($tempoAtendimento <= $sla->getHour()){
          $countPrazo++;
        }
      }

      //Percentual
      if($countConcluded == 0){
        $attendance->setStatusConcludedOnTimePercent(0);
        $attendance->setStatusConcludedOnTime(0);
      }else{
        $percentConcludedOnTime = ($countPrazo * 100)/$countConcluded;
        $attendance->setStatusConcludedOnTimePercent($percentConcludedOnTime);
        $attendance->setStatusConcludedOnTime($countPrazo);
      }
    }

    /**
     * Relatório de status por setor.
     * @Route("/status-setor", name="backend_report_status_sector")
     * @Template()
     */
    public function statusSectorAction(Request $request) {
      //Total de chamados pendentes
      $totalPendent = 0;
      //Total de chamados concluídos
      $totalConcluded = 0;
      //Total de chamados fora do prazo
      $totalFP = 0;
      //Total de chamados concluídos no prazo
      $totalConcludedOnTime = 0;
      //Total de chamados concluídos no prazo em percentual
      $totalConcludedOnTimePercent = 0;

      //Dados COMERCIAIS
      $pendentComercial = 0;
      $concludedComercial = 0;
      $fpComercial = 0;
      $concludedOnTimeComercial = 0;
      $concludedOnTimePercentComercial = 0;

      //Dados OPERAÇÃO
      $pendentOperacao = 0;
      $concludedOperacao = 0;
      $fpOperacao = 0;
      $concludedOnTimeOperacao = 0;
      $concludedOnTimePercentOperacao = 0;

      if ($request->getMethod() == 'POST') {
        $em = $this->getDoctrine()->getManager();
        //Busca a lista de atendimentos
        $attendanceList = $em->getRepository('AppBundle:Attendance')->findAll();

        $chamadoSearch = new ChamadoSearch();
        $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);
        $form->handleRequest($request);

        foreach ($attendanceList as $attendance) {
          $this->processStatusAttendanceTable($attendance, $chamadoSearch);
          $totalPendent+=$attendance->getStatusPendent();
          $totalConcluded+=$attendance->getStatusConcluded();
          $totalFP+=$attendance->getStatusOuttatime();
          $totalConcludedOnTime+=$attendance->getStatusConcludedOnTime();

          if($attendance->getId() == Attendance::ATTENDANCE_ORCAMENTO ||
            $attendance->getId() == Attendance::ATTENDANCE_ATIVO ||
            $attendance->getId() == Attendance::ATTENDANCE_RECEPTIVO){
            //Tipo COMERCIAL
            $pendentComercial+=$attendance->getStatusPendent();
            $concludedComercial+=$attendance->getStatusConcluded();
            $fpComercial+=$attendance->getStatusOuttatime();
            $concludedOnTimeComercial+=$attendance->getStatusConcludedOnTime();
          }else{
            //Tipo OPERAÇÃO
            $pendentOperacao+=$attendance->getStatusPendent();
            $concludedOperacao+=$attendance->getStatusConcluded();
            $fpOperacao+=$attendance->getStatusOuttatime();
            $concludedOnTimeOperacao+=$attendance->getStatusConcludedOnTime();
          }
        }
        if($totalConcluded == 0){
          $totalConcludedOnTimePercent = 0;
        }else{
          $totalConcludedOnTimePercent = ($totalConcludedOnTime*100)/$totalConcluded;
        }
        $entities = $attendanceList;
      }else{
        //Obtém o primeiro dia do mês
        $begin_date = new \DateTime('first day of this month');
        //Obtém o último dia do mês
        $last_date = new \DateTime('now');

        //Seta os valores padrões no objeto
        $chamadoSearch = new ChamadoSearch();
        /*$chamadoSearch->setBeginDate($begin_date);*/
        $chamadoSearch->setEndDate($last_date);

        $em = $this->getDoctrine()->getManager();
        //Busca a lista de atendimentos
        $attendanceList = $em->getRepository('AppBundle:Attendance')->findAll();
        $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);
        foreach ($attendanceList as $attendance) {
          $this->processStatusAttendanceTable($attendance, $chamadoSearch);
          $totalPendent+=$attendance->getStatusPendent();
          $totalConcluded+=$attendance->getStatusConcluded();
          $totalFP+=$attendance->getStatusOuttatime();
          $totalConcludedOnTime+=$attendance->getStatusConcludedOnTime();

          if($attendance->getId() == Attendance::ATTENDANCE_ORCAMENTO ||
            $attendance->getId() == Attendance::ATTENDANCE_ATIVO ||
            $attendance->getId() == Attendance::ATTENDANCE_RECEPTIVO){
            //Tipo COMERCIAL
            $pendentComercial+=$attendance->getStatusPendent();
            $concludedComercial+=$attendance->getStatusConcluded();
            $fpComercial+=$attendance->getStatusOuttatime();
            $concludedOnTimeComercial+=$attendance->getStatusConcludedOnTime();
          }else{
            //Tipo OPERAÇÃO
            $pendentOperacao+=$attendance->getStatusPendent();
            $concludedOperacao+=$attendance->getStatusConcluded();
            $fpOperacao+=$attendance->getStatusOuttatime();
            $concludedOnTimeOperacao+=$attendance->getStatusConcludedOnTime();
          }
        }
        if($totalConcluded == 0){
          $totalConcludedOnTimePercent = 0;
        }else{
          $totalConcludedOnTimePercent = ($totalConcludedOnTime*100)/$totalConcluded;
        }
        $entities = $attendanceList;

      }
      if($concludedComercial == 0){
        $concludedOnTimePercentComercial = 0;
      }else{
        $concludedOnTimePercentComercial = ($concludedOnTimeComercial*100)/$concludedComercial;
      }

      if($concludedOperacao == 0){
        $concludedOnTimePercentOperacao = 0;
      }else{
        $concludedOnTimePercentOperacao = ($concludedOnTimeOperacao*100)/$concludedOperacao;
      }

      return array(
        'entities' => $entities,
        'totalConcluded' => $totalConcluded,
        'totalFP' => $totalFP,
        'totalPendent' => $totalPendent,
        'totalConcludedOnTime' => $totalConcludedOnTime,
        'totalConcludedOnTimePercent' => $totalConcludedOnTimePercent,
        'pendentComercial' => $pendentComercial,
        'concludedComercial' => $concludedComercial,
        'fpComercial' => $fpComercial,
        'concludedOnTimeComercial' => $concludedOnTimeComercial,
        'concludedOnTimePercentComercial' => $concludedOnTimePercentComercial,
        'pendentOperacao' => $pendentOperacao,
        'concludedOperacao' => $concludedOperacao,
        'fpOperacao' => $fpOperacao,
        'concludedOnTimeOperacao' => $concludedOnTimeOperacao,
        'concludedOnTimePercentOperacao' => $concludedOnTimePercentOperacao,
        'form'=> $form->createView()
      );
    }

    /**
     * Relatório de status por setor.
     * @Route("/status-setor-export", name="backend_report_status_sector_export")
     * @Template()
     */
    public function statusSectorExportAction(Request $request) {
      //Total de chamados pendentes
      $totalPendent = 0;
      //Total de chamados concluídos
      $totalConcluded = 0;
      //Total de chamados fora do prazo
      $totalFP = 0;
      //Total de chamados concluídos no prazo
      $totalConcludedOnTime = 0;
      //Total de chamados concluídos no prazo em percentual
      $totalConcludedOnTimePercent = 0;

      //Dados COMERCIAIS
      $pendentComercial = 0;
      $concludedComercial = 0;
      $fpComercial = 0;
      $concludedOnTimeComercial = 0;
      $concludedOnTimePercentComercial = 0;

      //Dados OPERAÇÃO
      $pendentOperacao = 0;
      $concludedOperacao = 0;
      $fpOperacao = 0;
      $concludedOnTimeOperacao = 0;
      $concludedOnTimePercentOperacao = 0;

      if ($request->getMethod() == 'POST') {
        $em = $this->getDoctrine()->getManager();
        //Busca a lista de atendimentos
        $attendanceList = $em->getRepository('AppBundle:Attendance')->findAll();

        $chamadoSearch = new ChamadoSearch();
        $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);
        $form->handleRequest($request);

        foreach ($attendanceList as $attendance) {
          $this->processStatusAttendanceTable($attendance, $chamadoSearch);
          $totalPendent+=$attendance->getStatusPendent();
          $totalConcluded+=$attendance->getStatusConcluded();
          $totalFP+=$attendance->getStatusOuttatime();
          $totalConcludedOnTime+=$attendance->getStatusConcludedOnTime();

          if($attendance->getId() == Attendance::ATTENDANCE_ORCAMENTO ||
            $attendance->getId() == Attendance::ATTENDANCE_ATIVO ||
            $attendance->getId() == Attendance::ATTENDANCE_RECEPTIVO){
            //Tipo COMERCIAL
            $pendentComercial+=$attendance->getStatusPendent();
            $concludedComercial+=$attendance->getStatusConcluded();
            $fpComercial+=$attendance->getStatusOuttatime();
            $concludedOnTimeComercial+=$attendance->getStatusConcludedOnTime();
          }else{
            //Tipo OPERAÇÃO
            $pendentOperacao+=$attendance->getStatusPendent();
            $concludedOperacao+=$attendance->getStatusConcluded();
            $fpOperacao+=$attendance->getStatusOuttatime();
            $concludedOnTimeOperacao+=$attendance->getStatusConcludedOnTime();
          }
        }
        if($totalConcluded == 0){
          $totalConcludedOnTimePercent = 0;
        }else{
          $totalConcludedOnTimePercent = ($totalConcludedOnTime*100)/$totalConcluded;
        }
        $entities = $attendanceList;
      }else{
        //Obtém o primeiro dia do mês
        $begin_date = new \DateTime('first day of this month');
        //Obtém o último dia do mês
        $last_date = new \DateTime('last day of this month');

        //Seta os valores padrões no objeto
        $chamadoSearch = new ChamadoSearch();
        $chamadoSearch->setBeginDate($begin_date);
        $chamadoSearch->setEndDate($last_date);

        $em = $this->getDoctrine()->getManager();
        //Busca a lista de atendimentos
        $attendanceList = $em->getRepository('AppBundle:Attendance')->findAll();
        $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);
        foreach ($attendanceList as $attendance) {
          $this->processStatusAttendanceTable($attendance, $chamadoSearch);
          $totalPendent+=$attendance->getStatusPendent();
          $totalConcluded+=$attendance->getStatusConcluded();
          $totalFP+=$attendance->getStatusOuttatime();
          $totalConcludedOnTime+=$attendance->getStatusConcludedOnTime();

          if($attendance->getId() == Attendance::ATTENDANCE_ORCAMENTO ||
            $attendance->getId() == Attendance::ATTENDANCE_ATIVO ||
            $attendance->getId() == Attendance::ATTENDANCE_RECEPTIVO){
            //Tipo COMERCIAL
            $pendentComercial+=$attendance->getStatusPendent();
            $concludedComercial+=$attendance->getStatusConcluded();
            $fpComercial+=$attendance->getStatusOuttatime();
            $concludedOnTimeComercial+=$attendance->getStatusConcludedOnTime();
          }else{
            //Tipo OPERAÇÃO
            $pendentOperacao+=$attendance->getStatusPendent();
            $concludedOperacao+=$attendance->getStatusConcluded();
            $fpOperacao+=$attendance->getStatusOuttatime();
            $concludedOnTimeOperacao+=$attendance->getStatusConcludedOnTime();
          }
        }
        if($totalConcluded == 0){
          $totalConcludedOnTimePercent = 0;
        }else{
          $totalConcludedOnTimePercent = ($totalConcludedOnTime*100)/$totalConcluded;
        }
        $entities = $attendanceList;

      }
      if($concludedComercial == 0){
        $concludedOnTimePercentComercial = 0;
      }else{
        $concludedOnTimePercentComercial = ($concludedOnTimeComercial*100)/$concludedComercial;
      }

      if($concludedOperacao == 0){
        $concludedOnTimePercentOperacao = 0;
      }else{
        $concludedOnTimePercentOperacao = ($concludedOnTimeOperacao*100)/$concludedOperacao;
      }

      // ask the service for a Excel5
      $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

      $phpExcelObject->getProperties()->setCreator("Sistema de chamados")
      ->setTitle("Status por setor");
      $phpExcelObject->setActiveSheetIndex(0)
      ->setCellValue('A1', 'Tipo de Atendimento')
      ->setCellValue('B1', 'Total Pendente')
      ->setCellValue('C1', 'Total FP')
      ->setCellValue('D1', 'Total Concluído')
      ->setCellValue('E1', '% Concluído no prazo');
      $phpExcelObject->getActiveSheet()->setTitle('Status por setor');
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $phpExcelObject->setActiveSheetIndex(0);

      //Comercial
      $phpExcelObject->setActiveSheetIndex(0)->setCellValue("A2", "Comercial");
      $phpExcelObject->setActiveSheetIndex(0)->setCellValue("B2", $pendentComercial);
      $phpExcelObject->setActiveSheetIndex(0)->setCellValue("C2", $fpComercial);
      $phpExcelObject->setActiveSheetIndex(0)->setCellValue("D2", $concludedComercial);
      $phpExcelObject->setActiveSheetIndex(0)->setCellValue("E2", $concludedOnTimePercentComercial);

      //Operação
      $phpExcelObject->setActiveSheetIndex(0)->setCellValue("A3", "Operação");
      $phpExcelObject->setActiveSheetIndex(0)->setCellValue("B3", $pendentOperacao);
      $phpExcelObject->setActiveSheetIndex(0)->setCellValue("C3", $fpOperacao);
      $phpExcelObject->setActiveSheetIndex(0)->setCellValue("D3", $concludedOperacao);
      $phpExcelObject->setActiveSheetIndex(0)->setCellValue("E3", $concludedOnTimePercentOperacao);

      //Total Geral
      $phpExcelObject->setActiveSheetIndex(0)->setCellValue("A4", "Total Geral");
      $phpExcelObject->setActiveSheetIndex(0)->setCellValue("B4", $totalPendent);
      $phpExcelObject->setActiveSheetIndex(0)->setCellValue("C4", $totalFP);
      $phpExcelObject->setActiveSheetIndex(0)->setCellValue("D4", $totalConcluded);
      $phpExcelObject->setActiveSheetIndex(0)->setCellValue("E4", $totalConcludedOnTimePercent);

      //Autosize nas colunas da planilha
      foreach (range('A','E') as $columnID) {
        $phpExcelObject->getActiveSheet()->getColumnDimension($columnID)
        ->setAutoSize(true);
      }

      // create the writer
      $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
      // create the response
      $response = $this->get('phpexcel')->createStreamedResponse($writer);
      // adding headers
      $dispositionHeader = $response->headers->makeDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        'status-setor.xls'
      );
      $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
      $response->headers->set('Pragma', 'public');
      $response->headers->set('Cache-Control', 'maxage=1');
      $response->headers->set('Content-Disposition', $dispositionHeader);

      return $response;
    }

    /**
     * Relatório de chamados entrantes diários
     * @Route("/chamados-entrantes-diarios", name="backend_report_incoming_chamados")
     * @Template()
     */
    public function incomingChamadosAction(Request $request) {

      $em = $this->getDoctrine()->getManager();
      if ($request->getMethod() == 'POST') {
        $em = $this->getDoctrine()->getManager();

        $chamadoSearch = new ChamadoSearch();
        $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);
        $form->handleRequest($request);

        $comercialAttendance = Attendance::ATTENDANCE_ORCAMENTO.",".Attendance::ATTENDANCE_ATIVO.",".Attendance::ATTENDANCE_RECEPTIVO;
        $operationAttendance = Attendance::ATTENDANCE_MANUTENCAO.",".Attendance::ATTENDANCE_REPARO.",".Attendance::ATTENDANCE_INSTALACAO.",".Attendance::ATTENDANCE_OUTROS;

        //Busca o total de atendimentos
        $totalList = $em->getRepository('AppBundle:Chamado')->countByDay($chamadoSearch);
        $comercialList = $em->getRepository('AppBundle:Chamado')->countByDay($chamadoSearch, $comercialAttendance);
        $operationList = $em->getRepository('AppBundle:Chamado')->countByDay($chamadoSearch, $operationAttendance);
      }else{
        //Obtém o primeiro dia do mês
        $begin_date = new \DateTime('first day of this month');
        //Obtém o último dia do mês
        $last_date = new \DateTime('last day of this month');

        //Seta os valores padrões no objeto
        $chamadoSearch = new ChamadoSearch();
        $chamadoSearch->setBeginDate($begin_date);
        $chamadoSearch->setEndDate($last_date);

        $em = $this->getDoctrine()->getManager();
        //Busca a lista de atendimentos
        $attendanceList = $em->getRepository('AppBundle:Attendance')->findAll();
        $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);

        $comercialAttendance = Attendance::ATTENDANCE_ORCAMENTO.",".Attendance::ATTENDANCE_ATIVO.",".Attendance::ATTENDANCE_RECEPTIVO;
        $operationAttendance = Attendance::ATTENDANCE_MANUTENCAO.",".Attendance::ATTENDANCE_REPARO.",".Attendance::ATTENDANCE_INSTALACAO.",".Attendance::ATTENDANCE_OUTROS;

        //Busca o total de atendimentos
        $totalList = $em->getRepository('AppBundle:Chamado')->countByDay($chamadoSearch);
        $comercialList = $em->getRepository('AppBundle:Chamado')->countByDay($chamadoSearch, $comercialAttendance);
        $operationList = $em->getRepository('AppBundle:Chamado')->countByDay($chamadoSearch, $operationAttendance);
      }

      return array(
        'form'=> $form->createView(),
        'totalList' => $totalList,
        'comercialList' => $comercialList,
        'operationList' => $operationList,
      );
    }

    /**
     * Relatório de chamados entrantes diários
     * @Route("/chamados-entrantes-diarios-export", name="backend_report_incoming_chamados_export")
     * @Template()
     */
    public function incomingChamadosExportAction(Request $request) {

      $em = $this->getDoctrine()->getManager();
      if ($request->getMethod() == 'POST') {
        $em = $this->getDoctrine()->getManager();

        $chamadoSearch = new ChamadoSearch();
        $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);
        $form->handleRequest($request);

        $comercialAttendance = Attendance::ATTENDANCE_ORCAMENTO.",".Attendance::ATTENDANCE_ATIVO.",".Attendance::ATTENDANCE_RECEPTIVO;
        $operationAttendance = Attendance::ATTENDANCE_MANUTENCAO.",".Attendance::ATTENDANCE_REPARO.",".Attendance::ATTENDANCE_INSTALACAO.",".Attendance::ATTENDANCE_OUTROS;

        //Busca o total de atendimentos
        $totalList = $em->getRepository('AppBundle:Chamado')->countByDay($chamadoSearch);
        $comercialList = $em->getRepository('AppBundle:Chamado')->countByDay($chamadoSearch, $comercialAttendance);
        $operationList = $em->getRepository('AppBundle:Chamado')->countByDay($chamadoSearch, $operationAttendance);
      }else{
        //Obtém o primeiro dia do mês
        $begin_date = new \DateTime('first day of this month');
        //Obtém o último dia do mês
        $last_date = new \DateTime('last day of this month');

        //Seta os valores padrões no objeto
        $chamadoSearch = new ChamadoSearch();
        $chamadoSearch->setBeginDate($begin_date);
        $chamadoSearch->setEndDate($last_date);

        $em = $this->getDoctrine()->getManager();
        //Busca a lista de atendimentos
        $attendanceList = $em->getRepository('AppBundle:Attendance')->findAll();
        $form = $this->createForm(new ChamadoSearchType(), $chamadoSearch);

        $comercialAttendance = Attendance::ATTENDANCE_ORCAMENTO.",".Attendance::ATTENDANCE_ATIVO.",".Attendance::ATTENDANCE_RECEPTIVO;
        $operationAttendance = Attendance::ATTENDANCE_MANUTENCAO.",".Attendance::ATTENDANCE_REPARO.",".Attendance::ATTENDANCE_INSTALACAO.",".Attendance::ATTENDANCE_OUTROS;

        //Busca o total de atendimentos
        $totalList = $em->getRepository('AppBundle:Chamado')->countByDay($chamadoSearch);
        $comercialList = $em->getRepository('AppBundle:Chamado')->countByDay($chamadoSearch, $comercialAttendance);
        $operationList = $em->getRepository('AppBundle:Chamado')->countByDay($chamadoSearch, $operationAttendance);
      }

      // ask the service for a Excel5
      $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

      $phpExcelObject->getProperties()->setCreator("Sistema de chamados")
      ->setTitle("Chamados entrantes diários");
      $phpExcelObject->setActiveSheetIndex(0)
      ->setCellValue('A1', 'Setor')
      ->setCellValue('A2', 'Comercial')
      ->setCellValue('A3', 'Operação')
      ->setCellValue('A4', 'Total');
      $phpExcelObject->getActiveSheet()->setTitle('Chamados entrantes diários');
      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $phpExcelObject->setActiveSheetIndex(0);
      $charPos=1;
      $i=1;
      $j=4;
      foreach ($totalList as $total) {
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue($this->getNameFromNumber($charPos).$i, $total['date']);
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue($this->getNameFromNumber($charPos).$j, $total['total']);
        $charPos++;
      }

      $charPos=1;
      $i=2;
      foreach ($totalList as $total) {
        $valor = 0;
        foreach ($comercialList as $comercial) {
          if($comercial['date'] == $total['date']){
            $valor = $comercial['total'];
          }
        }
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue($this->getNameFromNumber($charPos).$i, $valor);
        $charPos++;
      }

      $charPos=1;
      $i=3;
      foreach ($totalList as $total) {
        $valor = 0;
        foreach ($operationList as $operation) {
          if($operation['date'] == $total['date']){
            $valor = $operation['total'];
          }
        }
        $phpExcelObject->setActiveSheetIndex(0)->setCellValue($this->getNameFromNumber($charPos).$i, $valor);
        $charPos++;
      }

      // create the writer
      $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
      // create the response
      $response = $this->get('phpexcel')->createStreamedResponse($writer);
      // adding headers
      $dispositionHeader = $response->headers->makeDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        'chamados-entrantes-diarios.xls'
      );
      $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
      $response->headers->set('Pragma', 'public');
      $response->headers->set('Cache-Control', 'maxage=1');
      $response->headers->set('Content-Disposition', $dispositionHeader);

      return $response;
    }

    public function getNameFromNumber($num) {
      $numeric = $num % 26;
      $letter = chr(65 + $numeric);
      $num2 = intval($num / 26);
      if ($num2 > 0) {
        return getNameFromNumber($num2 - 1) . $letter;
      } else {
        return $letter;
      }
    }
  }
