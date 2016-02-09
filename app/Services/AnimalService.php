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


                //categories - catalog
                $idsCat = explode(',', $data['categories']);
                $lstCategoriesIds = (count($idsCat) > 0) ? Category::idsIn($idsCat)->get(['id'])->toArray() : null;

                $catalogs = [];

                if ($lstCategoriesIds) {
                    foreach ($lstCategoriesIds as $key => $value) {
                        $catalogs[] = new Catalog([
                            'category_id' => $value['id'],
                            'tournament_id' => $idTournament
                        ]);
                    }

                    $oAnimal->catalogs()->saveMany($catalogs);
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