<?php

namespace App\Models;

class UserModel extends BaseModel
{

    public function getAll(): array
    {
        return $this->db->table("user")->get()->getResultObject();
    }

    public function getById(int $id): object|null
    {
        return $this->db->table("user")->getWhere(["id" => $id])->getRowObject();
    }

    public function getByIdFoyer(int $idFoyer): array
    {
        return $this->db->table("user")->getWhere(["idFoyer" => $idFoyer])->getResultObject();
    }

    public function getByIdRole(int $idRole): array
    {
        return $this->db->table("user")->getWhere(["idRole" => $idRole])->getResultObject();
    }

    public function getByIdRef(int $idRef): array
    {
        return $this->db->table("user")->getWhere(["idRef" => $idRef])->getResultObject();
    }

    public function getByLogin(string $login): object|null
    {
        return $this->db->table("user")->getWhere(["login" => $login])->getRowObject();
    }

    /**
     * This function adds a new user to a database table with the provided information and returns the
     * ID of the newly created user.
     * 
     * @param string lastname
     * @param string firstname
     * @param string login
     * @param string password
     * @param int idRole
     * @param int idFoyer
     * 
     * @return int ID of the newly added user or -1 if an error occurred
     */
    public function add(string $lastname, string $firstname, string $login, string $password, int $idRole, int $idFoyer): int
    {
        $data = [
            "id"        => $this->getMax("user", "id") + 1,
            "lastname"  => $lastname,
            "firstname" => $firstname,
            "login"     => $login,
            "password"  => $password,
            "idRole"    => $idRole,
            "idFoyer"   => $idFoyer
        ];

        $this->db->table("user")->insert($data);

        if ($this->isLastQuerySuccessfull()) {
            return $data["id"];
        } else {
            return -1;
        }
    }

    public function updateData(int $idUser, object $data): bool
    {
        $this->db->table("user")->update($data, ["id" => $idUser]);

        return $this->isLastQuerySuccessfull();
    }

    public function updateLastLogin(int $id): bool
    {
        $this->db->table("user")->update(["lastLogin" => date("Y-m-d H:i:s")], ["id" => $id]);

        return $this->isLastQuerySuccessfull();
    }

    public function updateLastLogout(int $id): bool
    {
        $this->db->table("user")->update(["lastLogout" => date("Y-m-d H:i:s")], ["id" => $id]);

        return $this->isLastQuerySuccessfull();
    }

    public function updatePassword(int $id, string $password): bool
    {
        $this->db->table("user")->update(["password" => $password], ["id" => $id]);

        return $this->isLastQuerySuccessfull();
    }

    public function setActive(int $id, int $value): bool
    {
        $this->db->table("user")->update(["active" => $value], ["id" => $id]);

        return $this->isLastQuerySuccessfull();
    }

    public function getProfilPicture(int $id): string | null
    {
        $result = $this->db->table("user")->select("pic")->getWhere(["id" => $id])->getRow();

        return $result == null ? null : $result->pic;
    }

    public function setProfilPicture(int $id, string|null $path): bool
    {
        $this->db->table("user")->update(["pic" => $path], ["id" => $id]);

        return $this->isLastQuerySuccessfull();
    }

    public function getAffinities(int $idUser): array | null
    {


        $subquery = $this->db->table('friend fr')
            ->select('1')
            ->where('fr.idUser1', $idUser)
            ->where('fr.idUser2 = u.id', null, false)
            ->orWhere('fr.idUser1 = u.id', null, false)
            ->where('fr.idUser2', $idUser)
            ->limit(1)
            ->getCompiledSelect();

        $query = $this->db->table('user u')
            ->select('u.id as idUser, u.lastname, u.firstname, u.login, u.pseudo, u.idRole, u.idFoyer')
            ->join('foyer f', 'u.idFoyer = f.id')
            ->where('u.id <>', $idUser)
            ->where("NOT EXISTS ($subquery)", null, false)
            ->whereNotIn('u.id', function ($builder) use ($idUser) {
                $builder->select('idBlocked')
                    ->from('blocked')
                    ->where('idUser', $idUser);
            })
            ->whereNotIn('u.id', function ($builder) use ($idUser) {
                $builder->select('idUser')
                    ->from('blocked')
                    ->where('idBlocked', $idUser);
            })
            ->where('f.id', function ($builder) use ($idUser) {
                $builder->select('idFoyer')
                    ->from('user')
                    ->where('id', $idUser);
            });

        $result = $query->get()->getResult();

        return $result;
    }

    public function getUsersByZip(string $zip): array | null
    {
        return $query = $this->db->table('user')
            ->select('user.*')
            ->join('foyer', 'user.idFoyer = foyer.id')
            ->where('LEFT(foyer.zip, 2)', $zip)
            ->get()->getResultObject();
    }
}
