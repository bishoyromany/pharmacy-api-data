<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RX extends Model
{
    use HasFactory;

    protected $table = "RxDetails";

    protected $fillable = [
        "PATIENTNO", "PATTYPE", "BILLINS", "BILLTYPE", "RXNO", "STATUS", "PAYSTATUS", "PRESNO", "NDC", "BRAND", "PRIORAPP", "DATEO", "DATEF", "QUANT", "DAYS", "TREFILLS",
        "NREFILL", "PRICE_CD", "DISC_CD", "AWP", "UnC", "BILLEDAMT", "AMOUNT", "PFEE", "OTHFEE", "OTHAMT", "DISCOUNT", "STAX", "COPAY", "TOTAMT", "BAL", "COMPOUND", "SUBNO",
        "SIG", "SIGLINES", "ORDSTATUS", "PHARMACIST", "STATION_ID", "COPAYPAID", "RXFILLTIME", "CRXSERNO", "BILLAS", "FILLNO", "FILLSTAT", "TRANSTYPE", "LMOD_DATE", "LMOD_TIME",
        "LMOD_BY", "PICKUPDATE", "PICKUPTIME", "PICKUPFROM", "ENTEREDDATE", "DRGNAME", "DRGMADE", "DRGTYPE", "STRONG", "FORM", "UNITS", "DBrand", "UM", "UDI", "QNTPACK", "UPRICE",
        "CLASS", "TRIPL_REQ", "MEDNO", "LNAME", "FNAME", "MI", "DOB", "SEX", "GROUPNO", "RELATION", "PERSON_CD", "ADDRSTR", "ADDRCT", "ADDRST", "ADDRZP", "PHONE", "FACILITYCD",
        "PRESLNM", "PRESFNM", "PRESLIC", "PRESTYP", "MCDIDNO", "PRESDEA", "PR_ADDRSTR", "PR_ADDRCT", "PR_ADDRST", "PR_ADDRZP", "PR_PHONE", "UPINNO", "REFREMIND", "QTY_ORD",
        "FILEDUEDATE", "UPRICE_C", "TIMEF", "DELIVERY", "TotBilledAmt", "PICKEDUP", "Active", "CreationDate", "ADDRSTRLINE2", "MOBILENO", "Cost", "Is340B", "TagId", "TECHNICIAN",
        "WORKNO", "Expr1", "Deceased", "TXRCODE", "TXRXCODE", "PACKAGEID", "PRODUCTID", "MARKETEDPRODUCTID", "SPECIFICPRODUCTID", "TherapeuticClassID", "PatTypeBinno",
        "PatTypeInsName", "ADDRESS", "CITY", "STATE", "ZIP", "PHONE_V", "PHARM_NO", "MAG_CODE", "MDREFILL", "RxEnteredBy", "NCPDPQuantityUnitOfMeasure", "PresFaxNo", "PresNpiNo",
        "InvBucketID"
    ];

    protected $guards = ["updated_at", "created_at"];
}
