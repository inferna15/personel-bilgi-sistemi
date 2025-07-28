<?php

namespace App\Enum;

enum LeaveType: string
{
    case ANNUAL = 'Yıllık İzin';
    case SICK = 'Hastalık İzni';
    case MATERNITY = 'Doğum İzni';
    case PATERNITY = 'Babalık İzni';
    case MARRIAGE = 'Evlenme İzni';
    case DEATH = 'Ölüm İzni';
    case UNPAID = 'Ücretsiz İzin';
    case ADMINISTRATIVE = 'İdari İzin';
    case EDUCATION = 'Eğitim İzni';
    case VOLUNTEER = 'Gönüllü İzin';
}
