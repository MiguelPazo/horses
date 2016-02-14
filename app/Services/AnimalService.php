<?php namespace Horses\Services;

use Horses\Agent;
use Horses\Animal;
use Horses\Catalog;
use Horses\Category;
use Horses\Constants\ConstDb;
use Horses\Constants\ConstMessages;
use Illuminate\Support\Facades\DB;

class AnimalService
{

    public function getInfo($id)
    {
        $jResponse = [
            'success' => false,
            'name' => null,
            'birthdate' => null,
            'code' => null,
            'owner' => null,
            'breeder' => null,
            'prefix' => null,
            'mom' => null,
            'dad' => null
        ];

        $oAnimal = Animal::with('agents')->find($id);

        if ($oAnimal) {
            $lstAnimal = Animal::with('agents')->idsIn([$oAnimal->mom, $oAnimal->dad])->get();
            $oMom = null;
            $oDad = null;
            $oMomBreeder = null;
            $oDadBreeder = null;

            $oOwner = $oAnimal->agents->filter(function ($item) {
                return $item->pivot->type = ConstDb::AGENT_OWNER;
            })->first();

            $oBreeder = $oAnimal->agents->filter(function ($item) {
                return $item->pivot->type = ConstDb::AGENT_BREEDER;
            })->first();

            if ($oAnimal->mom) {
                $oMom = $lstAnimal->filter(function ($item) use ($oAnimal) {
                    return $item->id == $oAnimal->mom;
                })->first();

                $oMomBreeder = $oMom->agents->filter(function ($item) {
                    return $item->type == ConstDb::AGENT_BREEDER;
                })->first();
            }

            if ($oAnimal->dad) {
                $oDad = $lstAnimal->filter(function ($item) use ($oAnimal) {
                    return $item->id == $oAnimal->dad;
                })->first();

                $oDadBreeder = $oDad->agents->filter(function ($item) {
                    return $item->type == ConstDb::AGENT_BREEDER;
                })->first();
            }

            $jResponse['success'] = true;
            $jResponse['id'] = $oAnimal->id;
            $jResponse['name'] = $oAnimal->name;
            $jResponse['birthdate'] = $oAnimal->birthdate;
            $jResponse['code'] = $oAnimal->code;
            $jResponse['owner'] = ($oOwner) ? $oOwner->names . ', ' . $oOwner->lastnames : null;
            $jResponse['breeder'] = ($oBreeder) ? $oBreeder->names . ', ' . $oBreeder->lastnames : null;
            $jResponse['prefix'] = ($oBreeder) ? $oBreeder->prefix : '';
            $jResponse['mom'] = ($oMom) ? (($oMomBreeder) ? '(' . $oMomBreeder->prefix . ') ' . $oMom->name : $oMom->name) : null;
            $jResponse['dad'] = ($oDad) ? (($oDadBreeder) ? '(' . $oDadBreeder->prefix . ') ' . $oDad->name : $oDad->name) : null;
        }

        return $jResponse;
    }

    public function save($data, $idTournament, $oAnimal = null)
    {
        $jResponse = [
            'success' => false,
            'message' => '',
            'url' => ''
        ];

        $name = strtoupper($data['name']);
        $code = strtoupper($data['code']);
        $birthdate = $data['birthdate'];
        $posMom = strpos($data['mom_name'], ')');
        $posDad = strpos($data['dad_name'], ')');
        $momName = strtoupper(trim(substr($data['mom_name'], ($posMom) ? $posMom + 1 : 0)));
        $dadName = strtoupper(trim(substr($data['dad_name'], ($posDad) ? $posDad + 1 : 0)));
        $prefix = strtoupper($data['prefix']);
        $next = true;
        $oOwner = null;
        $oBreeder = null;
        $oMom = null;
        $oDad = null;

        $withOwner = ($data['owner_name'] != '') ? true : false;
        $withBreeder = ($data['breeder_name'] != '') ? true : false;
        $withMom = ($momName != '') ? true : false;
        $withDad = ($dadName != '') ? true : false;

        $dataOwner = explode(',', strtoupper($data['owner_name']));
        $dataBreeder = explode(',', strtoupper($data['breeder_name']));

        $next = ($withOwner) ? ((count($dataOwner) != 2) ? false : true) : true;
        $next = ($withBreeder) ? ((count($dataBreeder) != 2) ? false : true) : true;

        if ($next) {
            $ownerNames = ($withOwner) ? trim($dataOwner[0]) : '';
            $ownerLastnames = ($withOwner) ? trim($dataOwner[1]) : '';
            $breederNames = ($withBreeder) ? trim($dataBreeder[0]) : '';
            $breederLastnames = ($withBreeder) ? trim($dataBreeder[1]) : '';

            $lstAgents = Agent::where(function ($query) use ($ownerNames, $ownerLastnames) {
                return $query->where('names', $ownerNames)
                    ->where('lastnames', $ownerLastnames);
            })->orWhere(function ($query) use ($breederNames, $breederLastnames) {
                return $query->where('names', $breederNames)
                    ->where('lastnames', $breederLastnames);
            })->get();

            DB::beginTransaction();

            try {
                //Owner
                if ($withOwner) {
                    $oOwner = $lstAgents->filter(function ($item) use ($ownerNames, $ownerLastnames) {
                        if ($item->names == $ownerNames && $item->lastnames == $ownerLastnames) {
                            return $item;
                        }
                    })->first();

                    if (!$oOwner) {
                        $oOwner = Agent::create([
                            'names' => $ownerNames,
                            'lastnames' => $ownerLastnames
                        ]);
                    }
                }

                //Breeder
                if ($withBreeder) {
                    $oBreeder = $lstAgents->filter(function ($item) use ($breederNames, $breederLastnames) {
                        if ($item->names == $breederNames && $item->lastnames == $breederLastnames) {
                            return $item;
                        }
                    })->first();

                    if (!$oBreeder) {
                        if ($oOwner->names == $breederNames && $oOwner->lastnames == $breederLastnames) {
                            $oOwner->prefix = strtoupper($data['prefix']);
                            $oOwner->save();
                            $oBreeder = $oOwner;
                        } else {
                            $oBreeder = Agent::create([
                                'prefix' => strtoupper($data['prefix']),
                                'names' => $breederNames,
                                'lastnames' => $breederLastnames
                            ]);
                        }
                    } else if ($oBreeder->prefix != $prefix) {
                        $oBreeder->prefix = $prefix;
                        $oBreeder->save();
                    }
                }

                $lstParents = Animal::name($momName)->name($dadName, true)->whereNull('deleted_at')->get();

                //Mom
                if ($withMom) {
                    $oMom = $lstParents->filter(function ($item) use ($momName) {
                        return $item->name == $momName;
                    })->first();

                    if (!$oMom) {
                        $oMom = Animal::create(['name' => $momName, 'gender' => ConstDb::GEN_FEMALE]);
                    }
                }

                //Dad
                if ($withDad) {
                    $oDad = $lstParents->filter(function ($item) use ($dadName) {
                        return $item->name == $dadName;
                    })->first();

                    if (!$oDad) {
                        $oDad = Animal::create(['name' => $dadName, 'gender' => ConstDb::GEN_MALE]);;
                    }
                }

                if ($oAnimal) {
                    $oAnimal->agents()->detach();
                    $oAnimal->catalogs()->delete();

                    $oAnimal->code = $code;
                    $oAnimal->name = $name;
                    $oAnimal->birthdate = $birthdate;
                    $oAnimal->mom = ($withMom) ? $oMom->id : null;
                    $oAnimal->dad = ($withDad) ? $oDad->id : null;
                    $oAnimal->save();
                } else {
                    $oAnimal = Animal::create([
                        'code' => $code,
                        'name' => $name,
                        'birthdate' => $birthdate,
                        'mom' => ($withMom) ? $oMom->id : null,
                        'dad' => ($withDad) ? $oDad->id : null,
                    ]);
                }

                //Agents
                $countAttach = (($withOwner) ? 1 : 0) + (($withBreeder) ? 1 : 0);

                switch ($countAttach) {
                    case 1:
                        if ($withOwner) {
                            $oAnimal->agents()->attach([
                                $oOwner->id => ['type' => ConstDb::AGENT_OWNER]
                            ]);
                        }

                        if ($withBreeder) {
                            $oAnimal->agents()->attach([
                                $oBreeder->id => ['type' => ConstDb::AGENT_BREEDER]
                            ]);
                        }
                        break;
                    case 2:
                        if ($oOwner->id == $oBreeder->id) {
                            $oAnimal->agents()->attach([
                                $oOwner->id => ['type' => ConstDb::AGENT_OWNER]
                            ]);
                            $oAnimal->agents()->attach([
                                $oBreeder->id => ['type' => ConstDb::AGENT_BREEDER]
                            ]);
                        } else {
                            $oAnimal->agents()->attach([
                                $oOwner->id => ['type' => ConstDb::AGENT_OWNER],
                                $oBreeder->id => ['type' => ConstDb::AGENT_BREEDER]
                            ]);
                        }
                        break;
                }

                //less count competitors in categories
                $idsCatOld = [];

                foreach ($oAnimal->catalogs as $key => $value) {
                    $idsCatOld[] = $value->category_id;
                }

                //categories - catalog
                $wCategories = array_key_exists('categories', $data);

                if ($wCategories) {
                    $idsCat = explode(',', $data['categories']);
                    $lstCategoriesIds = (count($idsCat) > 0) ? Category::idsIn($idsCat)->get() : null;

                    $catalogs = [];

                    if ($lstCategoriesIds) {
                        foreach ($lstCategoriesIds as $key => $value) {
                            $dataCatalog = [
                                'category_id' => $value->id,
                                'tournament_id' => $idTournament
                            ];

                            $catalogs[] = new Catalog($dataCatalog);
                        }

                        $oAnimal->catalogs()->saveMany($catalogs);
                    }

                    $idsCatPass = array_intersect($idsCatOld, $idsCat);
                    $idsLess = array_diff($idsCatOld, $idsCatPass);
                    $idsPlus = array_diff($idsCat, $idsCatPass);
                    $idsSearch = array_merge($idsLess, $idsPlus);

                    if (count($idsSearch) > 0) {
                        $lstCatUpdate = Category::idsIn($idsSearch)->lockForUpdate()->get();

                        foreach ($lstCatUpdate as $key => $value) {
                            if (in_array($value->id, $idsLess)) {
                                $value->count_competitors = ($value->count_competitors > 0) ? $value->count_competitors - 1 : 0;
                            } else if (in_array($value->id, $idsPlus)) {
                                $value->count_competitors = $value->count_competitors + 1;
                            }

                            $value->save();
                        }
                    }
                }

                DB::commit();

                $jResponse['success'] = true;
                $jResponse['url'] = route('oper.animal.index');
            } catch (Exception $ex) {
                DB::rollback();
                throw $ex;
            }
        } else {
            $jResponse['message'] = ConstMessages::NAME_WITHOUT_COMMA;
        }

        return $jResponse;
    }
}