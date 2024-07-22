<?php

namespace Supplycart\EInvoice\Enums;

enum Operation: string
{
    case LOGIN = 'login';
    case SET_ACCESS_TOKEN = 'setAccessToken';
    case GET_ACCESS_TOKEN = 'getAccessToken';
    case SET_ON_BEHALF_OF = 'setOnbehalfof';
    case GET_ALL_DOCUMENT_TYPES = 'getAllDocumentTypes';
    case GET_DOCUMENT_TYPE = 'getDocumentType';
    case GET_DOCUMENT_TYPE_VERSION = 'getDocumentTypeVersion';
    case GET_DOCUMENT = 'getDocument';
    case GET_DOCUMENT_DETAIL = 'getDocumentDetail';
    case GET_RECENT_DOCUMENTS = 'getRecentDocuments';
    case SEARCH_DOCUMENTS = 'searchDocuments';
    case CANCEL_DOCUMENT = 'cancelDocument';
    case REJECT_DOCUMENT = 'rejectDocument';
    case GET_SUBMISSION = 'getSubmission';
    case SUBMIT_DOCUMENT = 'submitDocument';
    case GET_NOTIFICATIONS = 'getNotifications';
    case VALIDATE_TAX_PAYER_TIN = 'validateTaxPayerTin';
}
