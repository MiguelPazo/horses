<?php namespace Horses\Constants;

class ConstDb
{
    const PROFILE_ADMIN = 'admin';
    const PROFILE_COMMISSAR = 'commissar';
    const PROFILE_JURY = 'jury';
    const PROFILE_OPERATOR = 'operator';
    const USER_DISCONNECTED = 0;
    const USER_CONECTED = 1;

    const STATUS_DELETED = 'deleted';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_ACTIVE = 'active';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_FINAL = 'final';

    const TYPE_CATEGORY_SELECTION = 'selection';
    const TYPE_CATEGORY_WSELECTION = 'wselection';

    const TYPE_TOURNAMENT_JURY = 'jury';
    const TYPE_TOURNAMENT_WJURY = 'wjury';

    const STAGE_ASSISTANCE = 'assistance';
    const STAGE_SELECTION = 'selection';
    const STAGE_CLASSIFY_1 = 'classify_1';
    const STAGE_CLASSIFY_2 = 'classify_2';
    const STAGE_STATUS_SAVE = 0;
    const STAGE_STATUS_CLOSE = 1;

    const JURY_NORMAL = 0;
    const JURY_DIRIMENT = 1;
}