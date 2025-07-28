// src/enums.ts
export enum Gender {
    MALE = "Erkek",
    FEMALE = "Kadın",
}

export enum LeaveType {
    ANNUAL = "Yıllık İzin",
    SICK = "Hastalık İzni",
    MATERNITY = "Doğum İzni",
    PATERNITY = "Babalık İzni",
    MARRIAGE = "Evlenme İzni",
    DEATH = "Ölüm İzni",
    UNPAID = "Ücretsiz İzin",
    ADMINISTRATIVE = "İdari İzin",
    EDUCATION = "Eğitim İzni",
    VOLUNTEER = "Gönüllü İzin"
}

export enum LeaveStatus {
    PENDING = "Beklemede",
    APPROVED = "Onaylandı",
    REJECTED = "Reddedildi"
}
